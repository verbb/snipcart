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
use workingconcept\snipcart\models\SnipcartCustomer;
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
use GuzzleHttp\Client;
use yii\base\Exception;

/**
 * Class SnipcartService
 *
 * @package workingconcept\snipcart\services
 */
class SnipcartService extends Component
{

    // Constants
    // =========================================================================

    const EVENT_BEFORE_RETURN_SHIPPING_RATES  = 'beforeReturnShippingRates';
    const EVENT_BEFORE_REQUEST_SHIPPING_RATES = 'beforeRequestShippingRates';
    const EVENT_PRODUCT_INVENTORY_CHANGE      = 'productInventoryChange';

    /**
     * @var string
     */
    protected static $apiBaseUrl = 'https://app.snipcart.com/api/';

    /**
     * @var
     */
    protected $settings;

    /**
     * @var bool
     */
    protected $isLinked;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->settings = Snipcart::$plugin->getSettings();
        $this->isLinked = isset($this->settings->secretApiKey);

        if ($this->isLinked)
        {
            $this->client = $this->getClient();
        }
    }


    // Public Method
    // =========================================================================

    public function getClient(): Client
    {
        return $this->client = new Client([
            'base_uri' => self::$apiBaseUrl,
            'auth' => [
                $this->settings->secretApiKey,
                'password'
            ],
            'headers' => [
                'Content-Type' => 'application/json; charset=utf-8',
                'Accept'       => 'application/json, text/javascript, */*; q=0.01',
            ],
            'verify' => false,
            'debug' => false
        ]);
    }

    /**
     * Get an order from Snipcart
     * 
     * @param int $orderId Snipcart order ID
     * 
     * @return SnipcartOrder
     * @throws Exception
     */
    public function getOrder($orderId): SnipcartOrder
    {
        return new SnipcartOrder($this->apiRequest("orders/{$orderId}"));
    }

    /**
     * Get an order's notifications from Snipcart
     * 
     * @param int $orderId Snipcart order ID
     * 
     * @return \stdClass|array Snipcart response object
     * @throws Exception
     */
    public function getOrderNotifications($orderId)
    {
        return $this->apiRequest('orders/' . $orderId . '/notifications');
    }

    /**
     * Get an order's refunds from Snipcart
     * 
     * @param int $orderId Snipcart order ID
     * 
     * @return \stdClass|array Snipcart response object
     * @throws Exception
     */
    public function getOrderRefunds($orderId)
    {
        return $this->apiRequest('orders/' . $orderId . '/refunds');
    }

    /**
     * List Snipcart orders by a range of dates supplied in $_POST (defaults to 30 days)
     * 
     * @param integer $page  page of results
     * @param integer $limit number of results per page
     * 
     * @return \stdClass|array
     * @throws Exception
     */
    public function listOrders($page = 1, $limit = 25)
    {
        $response = $this->apiRequest('orders', [
            'offset' => ($page - 1) * $limit,
            'limit'  => $limit,
            'from'   => date('c', $this->dateRangeStart()),
            'to'     => date('c', $this->dateRangeEnd())
        ]);

        foreach ($response->items as &$orderData)
        {
            $orderData = new SnipcartOrder($orderData);
        }

        return $response;
    }

    /**
     * Get Snipcart orders.
     *
     * @param array $params
     *
     * @return SnipcartOrder[]
     * @throws Exception
     */
    public function getOrders($params = []): array
    {
        $response = $this->fetchOrders($params);
        $orders = [];

        // TODO: support higher limit with multiple API calls
        // TODO: support params similar to Craft Elements

        foreach ($response->items as $orderData)
        {
            $orders[] = new SnipcartOrder($orderData);
        }

        return $orders;
    }

    /**
     * Get Snipcart orders with pagination info.
     *
     * @param int   $page
     * @param int   $limit
     * @param array $params
     * @return \stdClass
     * @throws Exception
     */
    public function getPaginatedOrders($page = 1, $limit = 25, $params = []): \stdClass
    {
        // TODO: replace uses of listOrders with this

        $params['offset'] = ($page - 1) * $limit;
        $params['limit']  = $limit;

        $response = $this->fetchOrders($params);

        foreach ($response->items as &$item)
        {
            $item = new SnipcartOrder($item);
        }

        return (object) [
            'items'      => $response->items,
            'totalItems' => $response->totalItems,
            'offset'     => $response->offset,
        ];
    }

    /**
     * Query the API for orders with the provided parameters.
     *
     * @param array $params
     *
     * @return \stdClass|array API response object or array of objects.
     * @throws Exception
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

        return $this->apiRequest('orders', $apiParams);
    }

    /**
     * List Snipcart orders by a range of dates supplied in $_POST (defaults to 30 days)
     * 
     * @param integer $page  page of results
     * @param integer $limit number of results per page
     * 
     * @return array
     * @throws Exception
     */
    public function listOrdersByDay($page = 1, $limit = 25): array
    {
        $orders = $this->listOrders($page, $limit);
        $ordersByDay = [];

        foreach ($orders->items as $order)
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

        return ! empty($ordersByDay) ? $ordersByDay : [];
    }

    /**
     * List Snipcart customers
     * 
     * @param integer $page  page of results
     * @param integer $limit number of results per page
     * 
     * @return \stdClass
     * @throws Exception
     */
    public function listCustomers($page = 1, $limit = 25): \stdClass
    {
        $customers = $this->apiRequest('customers', [
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
        $customers = $this->apiRequest('customers', [
            'name' => $keywords
        ]);

        foreach ($customers->items as &$customer)
        {
            $customer = new SnipcartCustomer($customer);
        }

        return $customers;
    }

    /**
     * List available coupons (not implemented)
     * 
     * @return \stdClass|array
     * @throws \Exception
     */
    public function listDiscounts()
    {
        return $this->apiRequest('discounts');
    }

    /**
     * List abandoned carts (not implemented)
     *
     * @return \stdClass|array
     * @throws Exception
     */
    public function listAbandoned()
    {
        return $this->apiRequest('carts/abandoned');
    }

    /**
     * List subscriptions (not implemented)
     *
     * @return \stdClass|array
     * @throws Exception
     */
    public function listSubscriptions()
    {
        return $this->apiRequest('subscriptions');
    }

    /**
     * Get a customer from Snipcart
     * 
     * @param int $customerId Snipcart customer ID
     * 
     * @return SnipcartCustomer
     * @throws \Exception
     */
    public function getCustomer($customerId): SnipcartCustomer
    {
        $response = $this->apiRequest('customers/' . $customerId);

        return new SnipcartCustomer($response);
    }

    /**
     * Get a given customer's order history
     * 
     * @param int $customerId Snipcart customer ID
     * 
     * @return \stdClass|array
     * @throws Exception
     */
    public function getCustomerOrders($customerId)
    {
        return $this->apiRequest('customers/' . $customerId . '/orders');
    }

    /**
     * Ask Snipcart whether its provided token is genuine
     * (We use this for webhook posts to be sure they came from Snipcart)
     *
     * Tokens are deleted after this call, so it can only be used once to verify,
     * and tokens also expire in one hour. Expect a 404 if the token is deleted
     * or if it expires.
     * 
     * @param string  $token  token to be validated, probably from $_POST['HTTP_X_SNIPCART_REQUESTTOKEN']
     * 
     * @return bool
     * @throws Exception
     */
    public function tokenIsValid($token)
    {
        $response = $this->apiRequest('requestvalidation/' . $token, null, false);

        return isset($response->token) && $response->token === $token;
    }

    public function dateRangeStart()
    {
        $param   = Craft::$app->request->getParam('startDate', false);
        $default = strtotime('-1 month');
        $stored  = Craft::$app->session->get('snipcartStartDate');

        if ($param)
        {
            $startDate = DateTimeHelper::toDateTime($param['date'])
                ->modify('+1 day')
                ->getTimestamp();
        }
        else
        {
            $startDate = $stored ?? $default;
        }

        Craft::$app->session->set('snipcartStartDate', $startDate);

        return $startDate;
    }

    public function dateRangeEnd()
    {
        $param    = Craft::$app->request->getParam('endDate', false);
        $default  = time();
        $stored   = Craft::$app->session->get('snipcartEndDate');

        if ($param)
        {
            $endDate = DateTimeHelper::toDateTime($param['date'])
                ->modify('+1 day')
                ->getTimestamp();
        }
        else
        {
            $endDate = $stored ?? $default;
        }

        Craft::$app->session->set('snipcartEndDate', $endDate);

        return $endDate;
    }

    public function searchKeywords()
    {
        $param  = Craft::$app->request->getParam('searchKeywords', false);
        $stored = Craft::$app->session->get('snipcartSearchKeywords');

        $keywords = $param ?? $stored ?? '';

        Craft::$app->session->set('snipcartSearchKeywords', $keywords);

        return $keywords;
    }

    /**
     * Return custom shipping rates for a nearly-finalized Snipcart order.
     *
     * @param SnipcartOrder $order
     *
     * @return SnipcartShippingRate[]
     */
    public function processShippingRates(SnipcartOrder $order): array
    {
        $includeShipStationRates = in_array(
            Settings::PROVIDER_SHIPSTATION,
            $this->settings->enabledProviders, true
        );

        $rateOptions = [];

        $package = $this->getOrderPackagingDetails($order);

        if ($includeShipStationRates)
        {
            $to     = Snipcart::$plugin->shipStation->getToFromSnipcartData($order);
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
                $rateOptions[] = new SnipcartShippingRate([
                    'cost'        => number_format($shipStationRate->shipmentCost + $shipStationRate->otherCost, 2),
                    'description' => $shipStationRate->serviceName,
                    'code'        => $shipStationRate->serviceCode
                ]);
            }
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

        return $rateOptions;
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
                'order' => $order,
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
     * See whether we're linked up to Snipcart
     * 
     * @return bool
     */
    public function isLinked(): bool
    {
        return $this->isLinked;
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
            if ($this->settings->productIdentifier && $element = $this->getProductElementById($item->id))
            {
                $elements[] = $element;

                if ($this->settings->reduceQuantitiesOnOrder && $this->settings->productInventoryField)
                {
                    Snipcart::$plugin->snipcart->reduceProductInventory($element, $item->quantity);
                    // TODO: reduce product inventory in ShipStation if necessary
                }
            }
        }

        if (isset($this->settings->notificationEmails))
        {
            return $this->sendOrderEmailNotification($elements, $order);
        }

        return true;
    }

    /**
     * Have Craft send email order notifications.
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
        if (is_array($this->settings->notificationEmails))
        {
            $emailAddresses = $this->settings->notificationEmails;
        }
        elseif (is_string($this->settings->notificationEmails))
        {
            $emailAddresses = explode(',', $this->settings->notificationEmails);
        }
        else
        {
            throw new Exception('Email notification setting must be string or array.');
        }

        $emails = [];

        foreach ($emailAddresses as $email)
        {
            $email = trim($email);

            if (filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                $emails[] = $email;
            }
        }

        $errors  = [];
        $message = new Message();

        // temporarily change template modes so we can render the plugin's template
        $view = Craft::$app->getView();
        $oldTemplateMode = $view->getTemplateMode();
        $view->setTemplateMode($view::TEMPLATE_MODE_CP);

        if (Craft::$app->getConfig()->general->devMode)
        {
            $heroImage = Craft::$app->assetManager->getPublishedUrl(
                '@workingconcept/snipcart/assetbundles/dist/img/order-complete-devmode.png',
                true
            );
        }
        else
        {
            $heroImage = Craft::$app->assetManager->getPublishedUrl(
                '@workingconcept/snipcart/assetbundles/dist/img/order-complete.png',
                true
            );
        }

        foreach ($emails as $address)
        {
            $settings = Craft::$app->systemSettings->getSettings('email');

            $message->setFrom([$settings['fromEmail'] => $settings['fromName']]);
            $message->setTo($address);
            $message->setSubject($order->cardHolderName . ' just placed an order');

            $messageHtml = $view->renderPageTemplate('snipcart/email/order', [
                'heroImage' => $heroImage,
                'order'     => $order,
                'elements'  => $elements,
                'settings'  => $this->settings
            ]);

            $emogrifier = new \Pelago\Emogrifier($messageHtml);
            $mergedHtml = $emogrifier->emogrify();

            $message->setHtmlBody($mergedHtml);

            if ( ! Craft::$app->mailer->send($message))
            {
                $problem = "Notification failed to send to {$address}!";
                Craft::warning($problem, 'snipcart');
                $errors[] = $problem;
            }
        }

        $view->setTemplateMode($oldTemplateMode);

        if (count($errors))
        {
            return $errors;
        }

        return true;
    }


    // Private Methods
    // =========================================================================

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

        if ($this->settings->productIdentifier === 'id')
        {
            $element = Entry::find()
                ->id($id)
                ->one();
        }
        else
        {
            $element = Entry::find()
                ->where($this->settings->productIdentifier, $id)
                ->one();
        }

        if ( ! empty($element))
        {
            return $element;
        }

        return false;
    }

    /**
     * Query the Snipcart API via Guzzle
     * 
     * @param  string $query    Snipcart API method (segment) to query
     * @param  array  $inData   any data that should be sent with the request; will be formatted as URL parameters or POST data
     * @param  bool   $useCache whether or not to cache responses
     * 
     * @return \stdClass|array Response data object or array of objects.
     *
     * @throws Exception Thrown if configuration doesn't allow interaction.
     */
    private function apiRequest($query = '', $inData = [], $useCache = true)
    {
        if ( ! $this->isLinked)
        {
            throw new Exception('Snipcart plugin is not configured.');
        }

        if ( ! empty($inData))
        {
            $query .= '?' . http_build_query($inData);
        }

        $cacheService = Craft::$app->getCache();
        $cacheKey = 'snicart_' . $query;

        // make sure our broader settings *and* local preference both allow cache use
        $useCache = $useCache && $this->settings->cacheResponses;

        if ($useCache && $cachedResponseData = $cacheService->get($cacheKey))
        {
            return $cachedResponseData;
        }

        $response = $this->client->get($query);
        $statusCode = $response->getStatusCode();

        if ($statusCode !== 200 && $statusCode !== 201)
        {
            throw new Exception('Uh oh! Snipcart responded with ' . $statusCode . '.');
        }

        // get response data as object
        $responseData = json_decode($response->getBody(), false);

        if ($this->settings->cacheResponses && $useCache)
        {
            $cacheService->set($cacheKey, $responseData, $this->settings->cacheDurationLimit);
        }

        return $responseData;
    }
}
