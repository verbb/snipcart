<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\services;

use workingconcept\snipcart\events\ShippingRateEvent;
use workingconcept\snipcart\Snipcart;
use workingconcept\snipcart\models\Order;
use workingconcept\snipcart\models\Notification;
use workingconcept\snipcart\models\Refund;
use workingconcept\snipcart\models\Package;
use workingconcept\snipcart\helpers\ModelHelper;
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
     * @event ShippingRateEvent Triggered before shipping rates are requested from any third parties.
     */
    const EVENT_BEFORE_REQUEST_SHIPPING_RATES = 'beforeRequestShippingRates';

    /**
     * @var string
     */
    const NOTIFICATION_TYPE_ADMIN = 'notifyAdmin';

    /**
     * @var string
     */
    const NOTIFICATION_TYPE_CUSTOMER = 'notifyCustomer';


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
            (array)$this->_fetchOrders($params)->items,
            Order::class
        );
    }

    /**
     * Get Snipcart orders using multiple requests if there are
     * pagination limits.
     *
     * @param array $params
     * @return array
     * @throws
     */
    public function getAllOrders($params = []): array
    {
        $collection = [];
        $collected = 0;
        $offset = 0;
        $finished = false;

        while ($finished === false)
        {
            $params['offset'] = $offset;

            if ($result = $this->_fetchOrders($params))
            {
                $currentItems = (array)$result->items;
                $collected += count($currentItems);
                $collection[] = $currentItems;

                if ($result->totalItems > $collected)
                {
                    $offset++;
                }
                else
                {
                    $finished = true;
                }
            }
            else
            {
                $finished = true;
            }
        }

        $items = array_merge(...$collection);

        return ModelHelper::populateArrayWithModels(
            $items,
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
     *              ->limit (int)
     * @throws \Exception if our API key is missing.
     */
    public function listOrders($page = 1, $limit = 25, $params = []): \stdClass
    {
        /**
         * define offset and limit since that's pretty much all we're doing here
         */
        $params['offset'] = ($page - 1) * $limit;
        $params['limit']  = $limit;

        $response = $this->_fetchOrders($params);

        return (object) [
            'items' => ModelHelper::populateArrayWithModels(
                $response->items,
                Order::class
            ),
            'totalItems' => $response->totalItems,
            'offset' => $response->offset,
            'limit' => $limit
        ];
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
            $event = new ShippingRateEvent([
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
     * @param Order  $order The relevant Snipcart order.
     * @param array  $extra Additional variables for email template.
     * @param string $type  Either `admin` or `customer`
     *
     * @return array|bool
     * @throws \Throwable if there's a template mode exception.
     */
    public function sendOrderEmailNotification($order, $extra = [], $type = self::NOTIFICATION_TYPE_ADMIN)
    {
        $errors        = [];
        $emailSettings = Craft::$app->systemSettings->getSettings('email');
        $view          = Craft::$app->getView();

        $templateSettings = $this->_selectNotificationTemplate($type);

        /**
         * Switch template mode only if we need to rely on our own template.
         */
        if ($templateSettings['user'] === false)
        {
            /**
             * Remember what we started with.
             */
            $templateMode = $view->getTemplateMode();

            /**
             * Explicitly set to CP mode.
             *
             * This could technically throw an exception over an invalid
             * template mode, but we're not worried.
             */
            $view->setTemplateMode($view::TEMPLATE_MODE_CP);
        }

        if ( ! $view->doesTemplateExist($templateSettings['path']))
        {
            /**
             * A custom template was specified that doesn't exist!
             */

            Craft::warning(sprintf(
                'Specified template `%s` does not exist.',
                $templateSettings['path']
            ), 'snipcart');
            
            return;
        }

        $emailVars = array_merge([
            'order'     => $order,
            'settings'  => Snipcart::$plugin->getSettings()
        ], $extra);

        // render the message
        $messageHtml = $view->renderPageTemplate(
            $templateSettings['path'],
            $emailVars
        );

        // inline the message's styles so they're more likely to be applied
        $emogrifier = new \Pelago\Emogrifier($messageHtml);
        $mergedHtml = $emogrifier->emogrify();

        $toEmails = [];
        $subject = $order->billingAddressName . ' just placed an order';

        if ($type === self::NOTIFICATION_TYPE_ADMIN)
        {
            $toEmails = Snipcart::$plugin->getSettings()->notificationEmails;
        }
        elseif ($type === self::NOTIFICATION_TYPE_CUSTOMER)
        {
            $toEmails = [ $order->email ];
            $subject = sprintf('%s Order #%s',
                Craft::$app->getSites()->getCurrentSite()->name,
                $order->invoiceNumber
            );
        }

        foreach ($toEmails as $address)
        {
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

        if ($templateSettings['user'] === false)
        {
            $view->setTemplateMode($templateMode);
        }

        if (count($errors))
        {
            return $errors;
        }

        return true;
    }

    /**
     * @param string $token          The order's unique identifier.
     * @param float  $amount         The amount of the refund.
     * @param string $comment        The reason for the refund.
     * @param bool   $notifyCustomer
     *
     * @return mixed
     * @throws \Exception if our API key is missing.
     */
    public function refundOrder($token, $amount, $comment = '', $notifyCustomer = false)
    {
        $refund = new Refund([
            'orderToken'     => $token,
            'amount'         => $amount,
            'comment'        => $comment,
            'notifyCustomer' => false,
        ]);

        $response = Snipcart::$plugin->api->post(
            sprintf('orders/%s/refunds', $token),
            $refund->getPayloadForPost()
        );

        return $response;
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
    private function _fetchOrders($params = [])
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

        $apiParams      = [];
        $hasCacheParam  = isset($params['cache']) && is_bool($params['cache']);
        $cacheSetting   = $hasCacheParam ? $params['cache'] : true;
        $dateTimeFormat = \DateTimeInterface::ATOM;

        if (isset($params['from']) && $params['from'] instanceof \DateTime)
        {
            $params['from'] = $params['from']->format($dateTimeFormat);
        }

        if (isset($params['to']) && $params['to'] instanceof \DateTime)
        {
            $params['to'] = $params['to']->format($dateTimeFormat);
        }

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

    /**
     * Select whatever Twig template should be used for an order notification,
     * and if it's a custom template make sure it exists before relying on it.
     *
     * @param string $type Either `admin` or `customer`.
     * @return array       Returns an array with a `path` string property
     *                     and `user` bool which is true if the template exists
     *                     on the front end—false if it's scoped to the plugin.
     */
    private function _selectNotificationTemplate($type): array
    {
        $settings = Snipcart::$plugin->getSettings();
        $defaultTemplatePath = '';
        $customTemplatePath = '';

        if ($type === self::NOTIFICATION_TYPE_ADMIN)
        {
            $defaultTemplatePath = 'snipcart/email/order';
            $customTemplatePath  = $settings->notificationEmailTemplate;
        }
        elseif ($type === self::NOTIFICATION_TYPE_CUSTOMER)
        {
            $defaultTemplatePath = 'snipcart/email/customer-order';
            $customTemplatePath  = $settings->notificationEmailTemplate;
        }

        $useCustom = ! empty($customTemplatePath);
        $templatePath = $useCustom ? $customTemplatePath : $defaultTemplatePath;

        return [
            'path' => $templatePath,
            'user' => $useCustom
        ];
    }

}
