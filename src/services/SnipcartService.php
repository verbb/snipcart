<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\services;

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
     * @return \stdClass Snipcart response object
     * @throws Exception
     */
    public function getOrderNotifications($orderId): \stdClass
    {
        return $this->apiRequest('orders/' . $orderId . '/notifications');
    }

    /**
     * Get an order's refunds from Snipcart
     * 
     * @param int $orderId Snipcart order ID
     * 
     * @return \stdClass Snipcart response object
     * @throws Exception
     */
    public function getOrderRefunds($orderId): \stdClass
    {
        return $this->apiRequest('orders/' . $orderId . '/refunds');
    }

    /**
     * List Snipcart orders by a range of dates supplied in $_POST (defaults to 30 days)
     * 
     * @param integer $page  page of results
     * @param integer $limit number of results per page
     * 
     * @return \stdClass
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
        $validParams = [
            'offset',
            'limit',
            'from',
            'to',
            'status',
            'invoiceNumber',
            'placedBy',
        ];

        // TODO: support params similar to Craft Elements
        // TODO: handle paginated responses invisibly

        $apiParams = [];
        $orders = [];

        foreach ($params as $key => $value)
        {
            if (in_array($key, $validParams, true))
            {
                $apiParams[$key] = $value;
            }
        }

        $response = $this->apiRequest('orders', $apiParams);

        foreach ($response->items as $orderData)
        {
            $orders[] = new SnipcartOrder($orderData);
        }

        return $orders;
    }

    public function getPaginatedOrders($page = 1, $limit = 25, $params = [])
    {
        // TODO: move listOrders here, update templates
        // TODO: make listOrders → getOrders, accept params and return

        $orders = [];

        $paging = [
            'totalItems' => null,
            'limit' => null,
            'offset' => null,
        ];
    }

    private function fetchOrders($params = [])
    {

    }

    /**
     * List Snipcart orders by a range of dates supplied in $_POST (defaults to 30 days)
     * 
     * @param integer $page  page of results
     * @param integer $limit number of results per page
     * 
     * @return array
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
     * @throws \Exception
     */
    public function listCustomers($page = 1, $limit = 25)
    {
        $customers = $this->apiRequest('customers', [
            'offset' => ($page - 1) * $limit,
            'limit'  => $limit
        ]);

        foreach ($customers->items as &$customer)
        {
            $customer = new SnipcartCustomer($customer);
        }

        return ! empty($customers) ? $customers : [];
    }

    /**
     * Search Snipcart customers
     *
     * @param integer $keywords  search term
     *
     * @return array
     * @throws \Exception
     */
    public function searchCustomers($keywords): array
    {
        $customers = $this->apiRequest('customers', [
            'name' => $keywords
        ]);

        return ! empty($customers) ? $customers : [];
    }

    /**
     * List available coupons (not implemented)
     * 
     * @return array
     * @throws \Exception
     */
    public function listDiscounts(): array
    {
        $discounts = $this->apiRequest('discounts');

        return ! empty($discounts) ? $discounts : [];
    }

    /**
     * List abandoned carts (not implemented)
     *
     * @return array
     * @throws \Exception
     */
    public function listAbandoned(): array
    {
        $abandoned = $this->apiRequest('carts/abandoned');

        return ! empty($abandoned) ? $abandoned : [];
    }

    /**
     * List subscriptions (not implemented)
     *
     * @return array
     * @throws \Exception
     */
    public function listSubscriptions(): array
    {
        $subscriptions = $this->apiRequest('subscriptions');

        return ! empty($subscriptions) ? $subscriptions : [];
    }

    /**
     * Get a customer from Snipcart
     * 
     * @param int $customerId Snipcart customer ID
     * 
     * @return \stdClass
     * @throws \Exception
     */
    public function getCustomer($customerId): \stdClass
    {
        return $this->apiRequest('customers/' . $customerId);
    }

    /**
     * Get a given customer's order history
     * 
     * @param int $customerId Snipcart customer ID
     * 
     * @return \stdClass
     * @throws \Exception
     */
    public function getCustomerOrders($customerId): \stdClass
    {
        return $this->apiRequest('customers/' . $customerId . '/orders');
    }

    /**
     * Ask Snipcart whether its provided token is genuine
     * (We use this for webhook posts to be sure they came from Snipcart)
     *
     * Tokens are deleted after this call, so it can only be used once to verify,
     * and tokens also expire in one hour—expect a 404 if the token is deleted
     * or if it expires
     * 
     * @param string  $token  $_POST['HTTP_X_SNIPCART_REQUESTTOKEN']
     * 
     * @return \stdClass
     * @throws \Exception
     */
    public function validateToken($token): \stdClass
    {
        return $this->apiRequest('requestvalidation/' . $token, null, false);
    }

    public function dateRangeStart()
    {
        $param   = Craft::$app->request->getParam('startDate', false);
        $default = strtotime('-1 month');
        $stored  = Craft::$app->session->get('snipcartStartDate');

        if ($param)
        {
            $startDate = strtotime($param['date']) + 86400;
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
            $endDate = strtotime($param['date']) + 86400;
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

        $packageDetails = $this->getOrderPackagingDetails($order);

        if ($includeShipStationRates)
        {
            $to     = Snipcart::$plugin->shipStation->getToFromSnipcartData($order);
            $weight = Snipcart::$plugin->shipStation->getWeightFromSnipcartData($order);

            if ( ! empty($packageDetails))
            {
                $dimensions = Snipcart::$plugin->shipStation->getDimensionsFromSnipcartData($packageDetails);

                if ( ! empty($packageDetails['weight']))
                {
                    // add the weight of the packaging if it's been specified
                    $weight->value += $packageDetails['weight'];
                }

                if ( ! empty($dimensions['length']) && ! empty($dimensions['width']) && ! empty($dimensions['height']))
                {
                    $shipStationRates = Snipcart::$plugin->shipStation->getRates($to, $weight, $dimensions);
                }
                else
                {
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
                'packaging' => $packageDetails
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
     * @return true or array of notification errors
     * @throws
     */
    public function updateElementsFromOrder(SnipcartOrder $order)
    {
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
     * @param Element[]  $elements  Craft Elements that represent order items.
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
            throw new \Exception('Email notification setting must be string or array.');
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

            try
            {
                Craft::$app->mailer->send($message);
            }
            catch (ErrorException $e)
            {
                $errors[] = $e;
            }
        }

        $view->setTemplateMode($oldTemplateMode);

        if (count($errors))
        {
            return $errors;
        }

        return true;
    }


    // ------------------------------------------------------------------------
    // Private Methods
    // ------------------------------------------------------------------------


    /**
     * Return a Craft Element that matches Snipcart's supplied product ID.
     *
     * @param  string $id  the unique ID Snipcart provided
     *
     * @return mixed       matching Craft Element or false
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
     * @return \stdClass|array|false|null  query response data, which can be a single object, an array,
     *                                     false if we're not linked, or void
     * @throws Exception
     */

    // TODO: clean up these response types
    
    private function apiRequest($query = '', $inData = array(), $useCache = true)
    {
        if ( ! $this->isLinked)
        {
            throw new Exception('Snipcart plugin is not configured.');
        }

        if (count($inData) > 0)
        {			
            $query .= '?' . http_build_query($inData);
        }

        if ($this->settings->cacheResponses && $useCache)
        {
            $cacheService = Craft::$app->getCache();
            $cacheKey = 'snicart:' . $query;

            if ($cachedResponseData = $cacheService->get($cacheKey))
            {
                return $cachedResponseData;
            }
        }

        try 
        {
            $response = $this->client->get($query);

            if ($response->getStatusCode() !== 200)
            {
                return;
            }

            $responseData = json_decode($response->getBody());

            if ($this->settings->cacheResponses && $useCache)
            {
                $cacheService->set($cacheKey, $responseData, $this->settings->cacheDurationLimit);
            }

            return $responseData;
        } 
        catch(\Exception $e)
        {
            return;
        }
    }
}
