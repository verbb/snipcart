<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\services;

use craft\helpers\DateTimeHelper;
use workingconcept\snipcart\models\Settings;
use workingconcept\snipcart\models\SnipcartAbandonedCart;
use workingconcept\snipcart\models\SnipcartCustomer;
use workingconcept\snipcart\models\SnipcartDiscount;
use workingconcept\snipcart\models\SnipcartRefund;
use workingconcept\snipcart\models\SnipcartNotification;
use workingconcept\snipcart\models\SnipcartSubscription;
use workingconcept\snipcart\Snipcart;
use workingconcept\snipcart\models\SnipcartShippingRate;
use workingconcept\snipcart\events\WebhookEvent;
use workingconcept\snipcart\events\InventoryEvent;
use workingconcept\snipcart\models\SnipcartOrder;
use workingconcept\snipcart\models\SnipcartPackage;

use Craft;
use craft\base\Component;
use craft\elements\Entry;
use craft\mail\Message;
use yii\base\Exception;

/**
 * Class SnipcartService
 *
 * For interacting with Snipcart via complete data models.
 *
 * @package workingconcept\snipcart\services
 */
class SnipcartService extends Component
{
    // TODO: clean up interfaces to be more Craft-y and obscure pagination concerns
    // TODO: return models with proper DateTime values
    // TODO: return null for invalid single-object requests, otherwise empty arrays

    // Constants
    // =========================================================================

    /**
     * @event WebhookEvent Triggered before shipping rates are requested from any third parties.
     */
    const EVENT_BEFORE_REQUEST_SHIPPING_RATES = 'beforeRequestShippingRates';

    /**
     * @event WebhookEvent Triggered before shipping rates are returned to Snipcart.
     */
    const EVENT_BEFORE_RETURN_SHIPPING_RATES  = 'beforeReturnShippingRates';

    /**
     * @event InventoryEvent Triggered when a product's inventory has changed as the result of an order.
     */
    const EVENT_PRODUCT_INVENTORY_CHANGE = 'productInventoryChange';


    // Public Methods
    // =========================================================================

    /**
     * Get a Snipcart order.
     * 
     * @param string $orderToken Snipcart order GUID
     * @return SnipcartOrder|null
     * @throws \Exception  Thrown when there isn't an API key to authenticate requests.
     */
    public function getOrder($orderToken)
    {
        if ($orderData = Snipcart::$plugin->api->get("orders/{$orderToken}"))
        {
            return new SnipcartOrder($orderData);
        }

        return null;
    }

    /**
     * Get Snipcart orders.
     *
     * @param array $params
     * @return SnipcartOrder[]
     * @throws \Exception  Thrown when there isn't an API key to authenticate requests.
     */
    public function getOrders($params = []): array
    {
        // TODO: support higher limit with multiple API calls
        // TODO: support params similar to Craft Elements

        return $this->populateArrayWithModels(
            $this->fetchOrders($params),
            SnipcartOrder::class
        );
    }

    /**
     * Get the notifications Snipcart has sent regarding a specific order.
     * 
     * @param string $orderToken Snipcart order ID
     * @return SnipcartNotification[]
     * @throws \Exception  Thrown when there isn't an API key to authenticate requests.
     */
    public function getOrderNotifications($orderToken): array
    {
        return $this->populateArrayWithModels(
            Snipcart::$plugin->api->get("orders/{$orderToken}/notifications"),
            SnipcartNotification::class
        );
    }

    /**
     * Get a Snipcart order's refunds.
     * 
     * @param int $orderToken Snipcart order ID
     * @return SnipcartRefund[]
     * @throws \Exception  Thrown when there isn't an API key to authenticate requests.
     */
    public function getOrderRefunds($orderToken): array
    {
        return $this->populateArrayWithModels(
            Snipcart::$plugin->api->get("orders/{$orderToken}/refunds"),
            SnipcartRefund::class
        );
    }

    /**
     * List Snipcart orders by a range of dates supplied in $_POST or $_SESSION (defaults to 30 days).
     * 
     * @param integer $page  page of results
     * @param integer $limit number of results per page
     * 
     * @return \stdClass
     * @throws \craft\errors\MissingComponentException Thrown if there's trouble getting a session further down.
     * @throws Exception Thrown when we don't have an API key with which to make calls.
     */
    public function listOrders($page = 1, $limit = 25): \stdClass
    {
        // TODO: rely on getPaginatedOrders for control panel views and get rid of this method

        $response = Snipcart::$plugin->api->get('orders', [
            'offset' => ($page - 1) * $limit,
            'limit'  => $limit,
            'from'   => date('c', $this->dateRangeStart()),
            'to'     => date('c', $this->dateRangeEnd())
        ]);

        $response->items = $this->populateArrayWithModels($response->items, SnipcartOrder::class);

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
     *              ->items (SnipcartOrder[])
     *              ->totalItems (int)
     *              ->offset (int)
     * @throws \Exception  Thrown when there isn't an API key to authenticate requests.
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
            'items'      => $this->populateArrayWithModels($response->items, SnipcartOrder::class),
            'totalItems' => $response->totalItems,
            'offset'     => $response->offset,
        ];
    }

    /**
     * List Snipcart orders by a range of dates supplied in $_POST (defaults to 30 days)
     *
     * @param integer $page  page of results
     * @param integer $limit number of results per page
     *
     * @return array
     * @throws \craft\errors\MissingComponentException Thrown if there's trouble getting a session further down.
     * @throws \Exception  Thrown when there isn't an API key to authenticate requests.
     */
    public function listOrdersByDay($page = 1, $limit = 25): array
    {
        // TODO: move reliance on $_POST/$_SESSION to controller

        $orders = $this->listOrders($page, $limit);
        $ordersByDay = [];

        foreach ($orders as $order)
        {
            $orderDate = \DateTime::createFromFormat('Y-m-d\TH:i:s\Z', $order->creationDate);
            $key = $orderDate->format('Y-m-d');

            if (isset($ordersByDay[$key]))
            {
                ++$ordersByDay[$key];
            }
            else
            {
                $ordersByDay[$key] = 1;
            }
        }

        return $ordersByDay;
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
     * @throws \Exception  Thrown when there isn't an API key to authenticate requests.
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

        $apiParams = [];

        foreach ($params as $key => $value)
        {
            if (in_array($key, $validParams, true))
            {
                $apiParams[$key] = $value;
            }
        }

        return Snipcart::$plugin->api->get('orders', $apiParams);
    }

    /**
     * List Snipcart customers.
     * 
     * @param integer $page  page of results
     * @param integer $limit number of results per page
     * 
     * @return \stdClass
     *              ->totalItems (int)
     *              ->offset (int)
     *              ->limit (int)
     *              ->items (SnipcartCustomer[])
     * @throws \Exception  Thrown when there isn't an API key to authenticate requests.
     */
    public function listCustomers($page = 1, $limit = 25): \stdClass
    {
        $customers = Snipcart::$plugin->api->get('customers', [
            'offset' => ($page - 1) * $limit,
            'limit'  => $limit
        ]);

        foreach ($customers->items as &$customer)
        {
            $customer = new SnipcartCustomer($customer);
        }

        return $customers;
    }

    /**
     * Search Snipcart customers
     *
     * @param integer $keywords  search term
     *
     * @return \stdClass
     * @throws \Exception
     */
    public function searchCustomers($keywords): \stdClass
    {
        $customers = Snipcart::$plugin->api->get('customers', [
            'name' => $keywords
        ]);

        foreach ($customers->items as &$customer)
        {
            $customer = new SnipcartCustomer($customer);
        }

        return $customers;
    }

    /**
     * List discounts.
     * 
     * @return SnipcartDiscount[]
     * @throws \Exception  Thrown when there isn't an API key to authenticate requests.
     */
    public function listDiscounts(): array
    {
        return $this->populateArrayWithModels(
            Snipcart::$plugin->api->get('discounts'),
            SnipcartDiscount::class
        );
    }

    /**
     * List abandoned carts.
     *
     * @return \stdClass|null
     * @throws \Exception  Thrown when there isn't an API key to authenticate requests.
     */
    public function listAbandonedCarts()
    {
        $abandonedCartData = Snipcart::$plugin->api->get('carts/abandoned');

        foreach ($abandonedCartData->items as &$abandonedCart)
        {
            $abandonedCart = new SnipcartAbandonedCart($abandonedCart);
        }

        return $abandonedCartData;
    }

    /**
     * List subscriptions.
     *
     * @return \stdClass|null
     * @throws \Exception  Thrown when there isn't an API key to authenticate requests.
     */
    public function listSubscriptions()
    {
        $subscriptionData = Snipcart::$plugin->api->get('subscriptions');

        foreach ($subscriptionData->items as &$subscription)
        {
            $subscription = new SnipcartSubscription($subscription);
        }

        return $subscriptionData;
    }

    /**
     * Get a customer from Snipcart
     * 
     * @param int $customerId Snipcart customer ID
     * @return SnipcartCustomer|null
     * @throws \Exception  Thrown when there isn't an API key to authenticate requests.
     */
    public function getCustomer($customerId): SnipcartCustomer
    {
        if ($customerData = Snipcart::$plugin->api->get("customers/{$customerId}"))
        {
            return new SnipcartCustomer($customerData);
        }

        return null;
    }

    /**
     * Get a given customer's order history
     * 
     * @param int $customerId Snipcart customer ID
     * 
     * @return SnipcartOrder[]
     * @throws \Exception  Thrown when there isn't an API key to authenticate requests.
     */
    public function getCustomerOrders($customerId): array
    {
        return $this->populateArrayWithModels(
            Snipcart::$plugin->api->get("customers/{$customerId}/orders"),
            SnipcartOrder::class
        );
    }

    /**
     * Get the beginning of the chosen date range.
     *
     * @return false|int|mixed
     * @throws \craft\errors\MissingComponentException
     */
    public function dateRangeStart()
    {
        // TODO: move this to helper or controller

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
     * @throws \craft\errors\MissingComponentException
     */
    public function dateRangeEnd()
    {
        // TODO: move this to helper or controller

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

    /**
     * Return custom shipping rates for a nearly-finalized Snipcart order.
     *
     * @param SnipcartOrder $order
     * @return SnipcartShippingRate[]
     */
    public function getShippingRatesForOrder(SnipcartOrder $order): array
    {
        $rateOptions = []; // to be populated
        $package     = $this->getOrderPackagingDetails($order);

        $includeShipStationRates = in_array(
            Settings::PROVIDER_SHIPSTATION,
            Snipcart::$plugin->getSettings()->enabledProviders,
            true
        );

        if ($includeShipStationRates)
        {
            $shipStationRates = $this->getShipStationRatesForSnipcartOrder($order, $package);
            
            $rateOptions = array_merge(
                $rateOptions,
                $shipStationRates
            );
        }

        if ($this->hasEventHandlers(self::EVENT_BEFORE_RETURN_SHIPPING_RATES))
        {
            $event = new WebhookEvent([
                'rates'     => $rateOptions,
                'order'     => $order,
                'packaging' => $package
            ]);

            $this->trigger(self::EVENT_BEFORE_RETURN_SHIPPING_RATES, $event);

            $rateOptions = $event->rates;
        }

        return [
            'rates'   => $rateOptions,
            'package' => $package,
        ];
    }

    /**
     * Trigger an Event that will allow another plugin or module to provide packaging
     * details for an order before shipping rates are requested.
     *
     * @param SnipcartOrder $order
     *
     * @return SnipcartPackage
     */
    public function getOrderPackagingDetails(SnipcartOrder $order): SnipcartPackage
    {
        $packageDetails = new SnipcartPackage();

        if ($this->hasEventHandlers(self::EVENT_BEFORE_REQUEST_SHIPPING_RATES))
        {
            $event = new WebhookEvent([
                'order'     => $order,
                'packaging' => $packageDetails,
            ]);

            $this->trigger(self::EVENT_BEFORE_REQUEST_SHIPPING_RATES, $event);

            $packageDetails = $event->packaging;
        }

        return $packageDetails;
    }

    /**
     * Trigger an Event that will allow another plugin or module to adjust
     * product inventory for a relevant Entry.
     *
     * @param Entry $entry entry that's used as a product definition
     * @param int   $quantity  a whole number representing the quantity change (normally negative)
     */
    public function reduceProductInventory($entry, $quantity)
    {
        if ($this->hasEventHandlers(self::EVENT_PRODUCT_INVENTORY_CHANGE))
        {
            $event = new InventoryEvent([
                'entry'    => $entry,
                'quantity' => - $quantity,
            ]);

            $this->trigger(self::EVENT_PRODUCT_INVENTORY_CHANGE, $event);
        }
    }

    /**
     * Get Craft Elements that relate to order items, updating quantities and sending a notification if relevant.
     *
     * @param SnipcartOrder $order
     *
     * @return bool|array  true if successful, or an array of notification errors
     * @throws
     */
    public function updateElementsFromOrder(SnipcartOrder $order)
    {
        $elements = [];

        // store up related entries, reducing inventory counts if needed
        foreach ($order->items as $item)
        {
            if (Snipcart::$plugin->getSettings()->productIdentifier && $element = $this->getProductElementById($item->id))
            {
                $elements[] = $element;

                if (Snipcart::$plugin->getSettings()->reduceQuantitiesOnOrder && Snipcart::$plugin->getSettings()->productInventoryField)
                {
                    $this->reduceProductInventory($element, $item->quantity);
                    // TODO: reduce product inventory in ShipStation if necessary
                }
            }
        }

        if (isset(Snipcart::$plugin->getSettings()->notificationEmails))
        {
            return $this->sendOrderEmailNotification($elements, $order);
        }

        return true;
    }


    // Private Methods
    // =========================================================================

    /**
     * Take an array of objects and turn each top-level element into an instance
     * of the given data model.
     *
     * @param array   $array  array where each item can be transformed into model
     * @param string  $class  name of desired model class
     * @return array
     */
    private function populateArrayWithModels(array $array, $class): array
    {
        foreach ($array as &$item)
        {
            $item = new $class($item);
        }

        return $array;
    }

    /**
     * Have Craft email order notifications.
     *
     * @param Entry[]  $elements  Craft Elements that represent order items.
     * @param SnipcartOrder $order
     *
     * @return array|bool
     * @throws \Throwable
     * @throws \yii\base\Exception
     */
    private function sendOrderEmailNotification($elements, $order)
    {
        $errors        = [];
        $emailSettings = Craft::$app->systemSettings->getSettings('email');
        
        // temporarily change template mode so we can render the plugin's template
        $view = Craft::$app->getView();
        $templateMode  = $view->getTemplateMode();
        $view->setTemplateMode($view::TEMPLATE_MODE_CP);

        // render the message
        $messageHtml = $view->renderPageTemplate('snipcart/email/order', [
            'order'     => $order,
            'elements'  => $elements,
            'settings'  => Snipcart::$plugin->getSettings()
        ]);
        
        // inline the message's styles so they have a fighting chance at rendering well
        $emogrifier = new \Pelago\Emogrifier($messageHtml);
        $mergedHtml = $emogrifier->emogrify();

        foreach (Snipcart::$plugin->getSettings()->notificationEmails as $address)
        {
            $message = new Message();

            $message->setFrom([$emailSettings['fromEmail'] => $emailSettings['fromName']]);
            $message->setTo($address);
            $message->setSubject($order->billingAddressName . ' just placed an order');
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
     * Get shipping rates from ShipStation based on the provided Snipcart
     * order and package.
     *
     * @param SnipcartOrder   $order
     * @param SnipcartPackage $package
     * 
     * @return SnipcartShippingRate[]
     */
    private function getShipStationRatesForSnipcartOrder(SnipcartOrder $order, SnipcartPackage $package): array
    {
        $rates  = [];
        $to     = Snipcart::$plugin->shipStation->getToFromSnipcartOrder($order);
        $weight = Snipcart::$plugin->shipStation->getWeightFromSnipcartOrder($order);

        if ($package !== null)
        {
            // translate SnipcartPackage into ShipStationDimensions
            $shipStationDimensions = Snipcart::$plugin->shipStation->getDimensionsFromSnipcartPackage($package);

            if ( ! empty($package->weight))
            {
                // add the weight of the packaging if it's been specified
                $weight->value += $package->weight;
            }

            if ($shipStationDimensions->hasPhysicalDimensions())
            {
                // pass dimensions for rate quote if we have them
                $shipStationRates = Snipcart::$plugin->shipStation->getRates($to, $weight, $shipStationDimensions);
            }
            else
            {
                // otherwise just get the quote based on weight only
                $shipStationRates = Snipcart::$plugin->shipStation->getRates($to, $weight);
            }
        }
        else
        {
            $shipStationRates = Snipcart::$plugin->shipStation->getRates($to, $weight);
        }

        foreach ($shipStationRates as $shipStationRate)
        {
            $rates[] = new SnipcartShippingRate([
                'cost'        => number_format($shipStationRate->shipmentCost + $shipStationRate->otherCost, 2),
                'description' => $shipStationRate->serviceName,
                'code'        => $shipStationRate->serviceCode
            ]);
        }

        return $rates;
    }

    /**
     * Return a Craft Element that matches Snipcart's supplied product ID.
     *
     * @param  string $id  the unique ID Snipcart provided
     *
     * @return Entry|false matching Craft Element or false
     */
    private function getProductElementById($id)
    {
        // TODO: support any Element type, not just Entry

        $productIdentifier = Snipcart::$plugin->getSettings()->productIdentifier;

        if ($productIdentifier === 'id')
        {
            $element = Entry::find()
                ->id($id)
                ->one();
        }
        else
        {
            $element = Entry::find()
                ->where($productIdentifier, $id)
                ->one();
        }

        if ( ! empty($element))
        {
            if (is_array($element))
            {
                return $element[0];
            }
            
            return $element;
        }

        return false;
    }
}
