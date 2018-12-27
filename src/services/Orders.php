<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\services;

use workingconcept\snipcart\Snipcart;
use workingconcept\snipcart\models\Order;
use workingconcept\snipcart\models\Notification;
use workingconcept\snipcart\models\Refund;
use workingconcept\snipcart\models\Package;
use workingconcept\snipcart\helpers\ModelHelper;
use workingconcept\snipcart\events\WebhookEvent;
use craft\mail\Message;
use Craft;
use craft\errors\MissingComponentException;
use craft\helpers\DateTimeHelper;

/**
 * The Orders service lets you interact with Snipcart orders as tidy,
 * documented models. The service can be accessed globally from
 * `Snipcart::$plugin->orders`.
 *
 * @package workingconcept\snipcart\services
 *
 * @todo clean up interfaces to be more Craft-y and obscure pagination concerns
 * @todo return null for invalid single-object requests, otherwise empty arrays
 */
class Orders extends \craft\base\Component
{
    // Constants
    // =========================================================================

    /**
     * @event WebhookEvent Triggered before shipping rates are requested from any third parties.
     */
    const EVENT_BEFORE_REQUEST_SHIPPING_RATES = 'beforeRequestShippingRates';

    // Public Methods
    // =========================================================================

    /**
     * Get a Snipcart order.
     *
     * @param string $orderToken Snipcart order GUID
     * @return Order|null
     * @throws \Exception if our API key is missing.
     */
    public function getOrder($orderToken)
    {
        if ($orderData = Snipcart::$plugin->api->get(sprintf(
            'orders/%s',
            $orderToken
        )))
        {
            return new Order((array)$orderData);
        }

        return null;
    }

    /**
     * Get Snipcart orders.
     *
     * @param array $params
     * @return Order[]
     * @throws \Exception if our API key is missing.
     *
     * @todo support higher limit with multiple API calls
     * @todo support params similar to Craft Elements
     */
    public function getOrders($params = []): array
    {
        return ModelHelper::populateArrayWithModels(
            (array)$this->fetchOrders($params)->items,
            Order::class
        );
    }

    /**
     * Get the notifications Snipcart has sent regarding a specific order.
     *
     * @param string $orderToken Snipcart order ID
     * @return Notification[]
     * @throws \Exception if our API key is missing.
     */
    public function getOrderNotifications($orderToken): array
    {
        return ModelHelper::populateArrayWithModels(
            (array)Snipcart::$plugin->api->get(sprintf(
                'orders/%s/notifications',
                $orderToken
            )),
            Notification::class
        );
    }

    /**
     * Get a Snipcart order's refunds.
     *
     * @param int $orderToken Snipcart order ID
     * @return Refund[]
     * @throws \Exception if our API key is missing.
     */
    public function getOrderRefunds($orderToken): array
    {
        return ModelHelper::populateArrayWithModels(
            (array)Snipcart::$plugin->api->get(sprintf(
                'orders/%s/refunds',
                $orderToken
            )),
            Refund::class
        );
    }

    /**
     * List Snipcart orders by a range of dates supplied in $_POST or $_SESSION
     * (defaults to 30 days).
     *
     * @param integer $page  page of results
     * @param integer $limit number of results per page
     *
     * @return \stdClass|array|null
     * @throws MissingComponentException if there's trouble getting a session
     *                                   further down.
     * @throws \Exception if our API key is missing.
     *
     * @todo rely on getPaginatedOrders for control panel views and get rid of this method
     */
    public function listOrders($page = 1, $limit = 25)
    {
        $response = Snipcart::$plugin->api->get('orders', [
            'offset' => ($page - 1) * $limit,
            'limit'  => $limit,
            'from'   => date('c', $this->dateRangeStart()),
            'to'     => date('c', $this->dateRangeEnd())
        ]);

        $response->items = ModelHelper::populateArrayWithModels(
            $response->items,
            Order::class
        );

        return $response;
    }

    /**
     * Get Snipcart orders with pagination info.
     *
     * @param int   $page
     * @param int   $limit
     * @param array $params
     *
     * @return \stdClass
     *              ->items (Order[])
     *              ->totalItems (int)
     *              ->offset (int)
     * @throws \Exception if our API key is missing.
     */
    public function getPaginatedOrders($page = 1, $limit = 25, $params = []): \stdClass
    {
        /**
         * define offset and limit since that's pretty much all we're doing here
         */
        $params['offset'] = ($page - 1) * $limit;
        $params['limit']  = $limit;

        $response = $this->fetchOrders($params);

        return (object) [
            'items' => ModelHelper::populateArrayWithModels(
                $response->items,
                Order::class
            ),
            'totalItems' => $response->totalItems,
            'offset' => $response->offset,
        ];
    }

    /**
     * List Snipcart orders by a range of dates supplied in $_POST
     * (defaults to 30 days).
     *
     * @param integer $page  page of results
     * @param integer $limit number of results per page
     *
     * @return array
     * @throws MissingComponentException if there's trouble getting a session
     *                                   further down.
     * @throws \Exception if our API key is missing.
     *
     * @todo move reliance on $_POST/$_SESSION to controller
     */
    public function listOrdersByDay($page = 1, $limit = 25): array
    {
        $orderData   = $this->listOrders($page, $limit);
        $ordersByDay = [];
        $start       = DateTimeHelper::toDateTime($this->dateRangeStart());
        $end         = DateTimeHelper::toDateTime($this->dateRangeEnd());

        $totalDays = $end->diff($start)->days;

        for ($i=0; $i <= $totalDays; $i++)
        {
            if ($i === 0)
            {
                $currentDay = $start;
            }
            else
            {
                $currentDay = $start->modify('+1 day');
            }

            $key = $currentDay->format('Y-m-d');

            if (! isset($ordersByDay[$key]))
            {
                $ordersByDay[$key] = 0;
            }

            foreach ($orderData->items as $order)
            {
                if ($order->completionDate->format('Y-m-d') == $key)
                {
                    ++$ordersByDay[$key];
                }
            }
        }
        
        return $ordersByDay;
    }

    /**
     * Get Craft Elements that relate to order items, updating quantities
     * and sending a notification if relevant.
     *
     * @param Order $order
     *
     * @return bool|array true if successful, or an array of notification errors
     * @throws
     */
    public function updateElementsFromOrder(Order $order)
    {
        if (
            Snipcart::$plugin->getSettings()->reduceQuantitiesOnOrder &&
            Snipcart::$plugin->getSettings()->productInventoryField
        )
        {
            foreach ($order->items as $item)
            {
                if ($item->getRelatedElement())
                {
                    Snipcart::$plugin->products->reduceProductInventory(
                        $item->getRelatedElement(),
                        $item->quantity
                    );
                    // TODO: reduce product inventory in ShipStation if necessary
                }
            }
        }

        return true;
    }

    /**
     * Trigger an Event that will allow another plugin or module to provide
     * packaging details for an order before shipping rates are requested.
     *
     * @param Order $order
     *
     * @return Package
     */
    public function getOrderPackaging(Order $order): Package
    {
        $package = new Package();

        if ($this->hasEventHandlers(self::EVENT_BEFORE_REQUEST_SHIPPING_RATES))
        {
            $event = new WebhookEvent([
                'order'   => $order,
                'package' => $package
            ]);
            $this->trigger(self::EVENT_BEFORE_REQUEST_SHIPPING_RATES, $event);
            $package = $event->package;
        }

        return $package;
    }

    /**
     * Have Craft email order notifications.
     *
     * @param Order $order The relevant Snipcart order.
     * @param array $extra Additional variables for email template.
     *
     * @return array|bool
     * @throws \Throwable if there's a template mode exception.
     */
    public function sendOrderEmailNotification($order, $extra = [])
    {
        $errors        = [];
        $emailSettings = Craft::$app->systemSettings->getSettings('email');

        /**
         * Temporarily change the template mode so we can render
         * the plugin's template.
         */
        $view         = Craft::$app->getView();
        $templateMode = $view->getTemplateMode();

        /**
         * This could technically throw an exception over an invalid
         * template mode, but we're not worried.
         */
        $view->setTemplateMode($view::TEMPLATE_MODE_CP);

        $emailVars = array_merge([
            'order'     => $order,
            'settings'  => Snipcart::$plugin->getSettings()
        ], $extra);

        // render the message
        $messageHtml = $view->renderPageTemplate(
            'snipcart/email/order',
            $emailVars
        );

        // inline the message's styles so they're more likely to be applied
        $emogrifier = new \Pelago\Emogrifier($messageHtml);
        $mergedHtml = $emogrifier->emogrify();

        foreach (Snipcart::$plugin->getSettings()->notificationEmails as $address)
        {
            $subject = $order->billingAddressName . ' just placed an order';
            $message = new Message();

            $message->setFrom([
                $emailSettings['fromEmail'] => $emailSettings['fromName']
            ]);
            $message->setTo($address);
            $message->setSubject($subject);
            $message->setHtmlBody($mergedHtml);

            if ( ! Craft::$app->mailer->send($message))
            {
                $problem = "Notification failed to send to {$address}!";
                Craft::warning($problem, 'snipcart');
                $errors[] = $problem;
            }
        }

        $view->setTemplateMode($templateMode);

        if (count($errors))
        {
            return $errors;
        }

        return true;
    }

    /**
     * Get the beginning of the chosen date range.
     *
     * @return false|int|mixed
     * @throws MissingComponentException
     * @todo move this to helper or controller
     */
    public function dateRangeStart()
    {
        $param   = Craft::$app->request->getParam('startDate', false);
        $default = strtotime('-1 month');
        $session = Craft::$app->getSession();

        if ($param)
        {
            $startDate = DateTimeHelper::toDateTime($param)->getTimestamp();
        }
        else
        {
            $startDate = $session->get('snipcartStartDate') ?? $default;
        }

        if ($session)
        {
            $session->set('snipcartStartDate', $startDate);
        }

        return $startDate;
    }

    /**
     * Get the end of the chosen date range.
     *
     * @return int|mixed
     * @throws MissingComponentException
     * @todo move this to helper or controller
     */
    public function dateRangeEnd()
    {
        $param    = Craft::$app->request->getParam('endDate', false);
        $default  = time();
        $session  = Craft::$app->getSession();

        if ($param)
        {
            $endDate = DateTimeHelper::toDateTime($param)->getTimestamp();
        }
        else
        {
            $endDate = $session->get('snipcartEndDate') ?? $default;
        }

        if ($session)
        {
            $session->set('snipcartEndDate', $endDate);
        }

        return $endDate;
    }

    /**
     * Return search keywords, checking params and then session vars.
     *
     * @return mixed|string
     * @throws \craft\errors\MissingComponentException
     */
    public function searchKeywords()
    {
        $param   = Craft::$app->getRequest()->getParam('searchKeywords', false);
        $session = Craft::$app->getSession();

        $keywords = $param ?? $session->get('snipcartSearchKeywords') ?? '';

        if ($session)
        {
            $session->set('snipcartSearchKeywords', $keywords);
        }

        return $keywords;
    }

    // Private Methods
    // =========================================================================

    /**
     * Query the API for orders with the provided parameters.
     * Invalid parameters are ignored and not sent to Snipcart.
     *
     * @param array $params
     *
     * @return \stdClass|array API response object or array of objects.
     * @throws \Exception if our API key is missing.
     */
    private function fetchOrders($params = [])
    {
        $validParams = [
            'offset',
            'limit',
            'from',
            'to',
            'status',
            'invoiceNumber',
            'placedBy',
        ];

        $apiParams     = [];
        $hasCacheParam = isset($params['cache']) && is_bool($params['cache']);
        $cacheSetting  = $hasCacheParam ? $params['cache'] : true;

        foreach ($params as $key => $value)
        {
            if (in_array($key, $validParams, true))
            {
                $apiParams[$key] = $value;
            }
        }

        return Snipcart::$plugin->api->get(
            'orders',
            $apiParams,
            $cacheSetting
        );
    }

}
