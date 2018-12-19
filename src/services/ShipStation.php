<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\services;

use workingconcept\snipcart\Snipcart;
use workingconcept\snipcart\models\shipstation\Dimensions;
use workingconcept\snipcart\models\shipstation\Order;
use workingconcept\snipcart\models\shipstation\Weight;
use workingconcept\snipcart\records\ShippingQuoteLog;
use workingconcept\snipcart\models\shipstation\Rate;
use workingconcept\snipcart\models\ShippingRate;
use workingconcept\snipcart\models\Package as SnipcartPackage;
use workingconcept\snipcart\models\Order as SnipcartOrder;

use Craft;
use yii\base\Exception;
use GuzzleHttp\Client;

/**
 * Class ShipStation
 *
 * @package workingconcept\snipcart\services
 */
class ShipStation extends \craft\base\Component
{
    // Constants
    // =========================================================================

    /**
     * @var string ShipStation's base API URL used for all interactions.
     */
    protected static $apiBaseUrl = 'https://ssapi.shipstation.com/';


    // Properties
    // =========================================================================

    protected $client;
    protected $shipFrom;
    protected $settings;
    protected $providerSettings;

    protected static $webhookOptions = [
        'ORDER_NOTIFY',
        'ITEM_ORDER_NOTIFY',
        'SHIP_NOTIFY',
        'ITEM_SHIP_NOTIFY'
    ];


    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();

        $this->settings         = Snipcart::$plugin->getSettings();
        $this->providerSettings = $this->settings->providers['shipStation'];
        $this->shipFrom         = $this->settings->shipFrom;

        if ($this->isLinked())
        {
            $this->client = new Client([
                'base_uri' => self::$apiBaseUrl,
                'auth' => [
                    $this->settings->providers['shipStation']['apiKey'],
                    $this->settings->providers['shipStation']['apiSecret']
                ],
                'headers' => [
                    'Content-Type' => 'application/json; charset=utf-8',
                    'Accept'       => 'application/json',
                ],
                'verify' => false,
                'debug' => false
            ]);
        }
        else
        {
            throw new Exception('Please add ShipStation API key and secret.');
        }
    }

    /**
     * Get shipping rates for the supplied order details.
     * https://www.shipstation.com/developer-api/#/reference/shipments/get-rates
     *
     * @param array                       $to [
     *                          		      'city'    => 'Seattle',
     *                          		      'state'   => 'WA',
     *                          		      'country' => 'US'
     *                          		      'zip'     => '98103'
     *                         	          ]
     * @param Weight           $weight
     * @param Dimensions|null  $dimensions (optional)
     * @param array                       $from (optional; defaults to standard shipFrom)
     * 
     * @return array
     * @throws \GuzzleHttp\Exception\ServerException
     */
    public function getRates($to, Weight $weight, Dimensions $dimensions = null, $from = []): array
    {
        $rates = [];

        if ($this->validateFrom($from))
        {
            $shipFrom = $from;
        }
        else
        {
            $shipFrom = $this->shipFrom;
        }

        // TODO: validate $to details so we're not wasting a request

        $shipmentInfo = [
            'carrierCode'    => $this->providerSettings['defaultCarrierCode'],
            //'serviceCode'  => '',
            'packageCode'    => $this->providerSettings['defaultPackageCode'],
            'fromPostalCode' => $shipFrom['postalCode'],
            'toCity'         => $to['city'],
            'toState'        => $to['state'], // 2-char state/province
            'toPostalCode'   => $to['zip'],
            'toCountry'      => $to['country'], // 2-char ISO country code
            'weight'         => $weight->toArray(),
            'confirmation'   => $this->providerSettings['defaultOrderConfirmation'],
            'residential'    => $to['residential'] ?? false
        ];

        if ($dimensions !== null)
        {
            $shipmentInfo['dimensions'] = $dimensions->toArray();
        }

        try
        {
            $response = $this->client->post('shipments/getrates', [
                \GuzzleHttp\RequestOptions::JSON => $shipmentInfo
            ]);
        }
        catch (\GuzzleHttp\Exception\ServerException $e)
        {
            /**
             * ShipStation returns a 500 error with a message if there aren't
             * any service options. It may also return a 500 if its app or
             * one of its providers experiences a technical problem, which can
             * include changed (and newly incorrect) sub-account credentials.
             */

            $responseBody = $e->getResponse()->getBody()->getContents() ?? null;

            if ($responseBody !== null)
            {
                // log the full, non-truncated error output if it's available
                Craft::error($e->getResponse()->getBody(), 'snipcart');
            }
            else
            {
                Craft::error($e, 'snipcart');
            }

            return $rates;
        }

        $response = json_decode($response->getBody());

        foreach ($response as $rateData)
        {
            $rates[] = new Rate($rateData);
        }

        return $rates;
    }

    /**
     * Create an order.
     * https://shipstation.docs.apiary.io/#reference/orders/createupdate-order/create/update-order
     *
     * @param Order $order
     *
     * @return Order|null decoded response data, with ->labelData
     *                    base64-encoded PDF body
     * @throws \Exception from Guzzle
     */
    public function createOrder(Order $order)
    {
        $payload = $order->toArray(
            [],
            $order->extraFields(),
            true
        );

        $payload = $this->removeReadOnlyFieldsFromPayload($payload);

        $response = $this->client->post('orders/createorder', [
            \GuzzleHttp\RequestOptions::JSON => $payload
        ]);

        /**
         * Handle problematic responses, where 200 and 201 are expected to be fine.
         * https://www.shipstation.com/developer-api/#/introduction/shipstation-api-requirements/server-responses
         */
        if ($response->getStatusCode() > 201)
        {
            /*
            switch ($response->getStatusCode())
            {
                case 204:
                    // No Content - The request was successful but there is no representation to return (that is, the response is empty).
                    break;
                case 400:
                    // Bad Request - The request could not be understood or was missing required parameters.
                    break;
                case 401:
                    // Unauthorized - Authentication failed or user does not have permissions for the requested operation.
                    break;
                case 403:
                    // Forbidden - Access denied.
                    break;
                case 404:
                    // Not Found - Resource was not found.
                    break;
                case 405:
                    // Method Not Allowed - Requested method is not supported for the specified resource.
                    break;
                case 429:
                    // Too Many Requests - Exceeded ShipStation API limits. When the limit is reached, your application should stop making requests until X-Rate-Limit-Reset seconds have elapsed.
                    break;
                case 500:
                    // Internal Server Error - ShipStation has encountered an error.
                    break;
            }
            */

            // something bad happened!
            Craft::warning($response->getBody(), 'snipcart');
            return null;
        }

        $responseData = json_decode($response->getBody(), true);

        return new Order($responseData);
    }

    /**
     * Attempt to figure out which shipping method the customer chose.
     *
     * @param Order  $order           Snipcart order.
     * @param string $shippingMethod  Formatted shipping name selected by
     *                                customer and provided by Snipcart,
     *                                to be used as a clue.
     * @return Rate|null
     */
    public function getShippingMethodFromOrder(Order $order, $shippingMethod)
    {
        $closest = null;

        /**
         * First try and find a matching rate quote, which would have preceded
         * the completed order.
         */
        $rateQuote = ShippingQuoteLog::find()
            ->where(['token' => $order->orderKey])
            ->orderBy(['dateCreated' => SORT_DESC])
            ->one();

        if ( ! empty($rateQuote))
        {
            // get the Snipcart order data
            $quoteRecord = json_decode($rateQuote->body);

            foreach ($quoteRecord->rates as $rate)
            {
                $rateAndServiceMatch = (float)$rate->cost === $order->shippingAmount
                    && $rate->description === $order->requestedShippingService;

                if ($rateAndServiceMatch)
                {
                    return new Rate([
                        'serviceName'  => $rate->description,
                        'serviceCode'  => $rate->code,
                        'shipmentCost' => $rate->cost,
                        'otherCost'    => 0,
                    ]);
                }
            }
        }

        /**
         * If there wasn't a matching option, query the API for rates again
         * and look for the closest match.
         */
        $rates = $this->getRates(
            [
                'city'    => $order->shipTo->city,
                'state'   => $order->shipTo->state,
                'country' => $order->shipTo->country,
                'zip'     => $order->shipTo->postalCode,
            ],
            $order->weight,
            $order->dimensions
        );

        // check rates for matching name and/or price, otherwise take closest
        foreach ($rates as $rate)
        {
            if ($rate->serviceName === $shippingMethod && $rate->shipmentCost === $order->shippingAmount)
            {
                // return exact match
                return $rate;
            }

            if ($closest === null)
            {
                $closest = $rate;
                continue;
            }

            $currentRateDifferenceFromPaid = abs($rate->shipmentCost - $order->shippingAmount);
            $closestRateDifferenceFromPaid = abs($closest->shipmentCost - $order->shippingAmount);

            if ($currentRateDifferenceFromPaid < $closestRateDifferenceFromPaid)
            {
                // use the rate that has the least cost difference
                $closest = $rate;
            }
        }

        return $closest;
    }


    /**
     * Create a label for an order, which will allow it to associate with order
     * details and populate a packing slip.
     *
     * Identical to createShipmentLabel() except for the required orderId.
     *
     * https://shipstation.docs.apiary.io/#reference/orders/create-label-for-order/create-label-for-order
     *
     * @param Order  $order
     * @param bool   $isTest       true if we only want to create a sample label
     * @param string $packageCode  package code to be sent
     *
     * @return \stdClass|null      response data, with ->labelData
     *                             base64-encoded PDF body
     */
    public function createLabelForOrder(Order $order, $isTest = false, $packageCode = 'package')
    {
        $payload = $order->toArray(
            [],
            $order->extraFields(),
            true
        );

        $payload['testLabel']   = $isTest;
        $payload['packageCode'] = $packageCode;

        $response = $this->client->post('orders/createlabelfororder', [
            \GuzzleHttp\RequestOptions::JSON => $payload
        ]);

        if ($response->getStatusCode() !== 200)
        {
            // something bad happened!
            return null;
        }

        return json_decode($response->getBody());
    }

    /**
     * https://www.shipstation.com/developer-api/#/reference/orders/list-orders/list-orders-w/o-parameters
     *
     * @param int $limit
     *
     * @return Order[]|null
     */
    public function listOrders($limit = 25): array
    {
        $response = $this->client->get(sprintf(
            'orders?pageSize=%d&sortBy=OrderDate&sortDir=DESC',
            $limit
        ));

        if ($response->getStatusCode() !== 200 && $response->getStatusCode() !== 201)
        {
            // something bad happened!
            Craft::warning('Failed to fetch ShipStation orders: ' . $response->getStatusCode());
            return [];
        }

        $orders = [];
        $responseData = json_decode($response->getBody(), true);

        foreach ($responseData['orders'] as $orderData)
        {
            $orders[] = new Order($orderData);
        }

        return $orders;
    }

    /**
     * Get an order by its order number, which is the Snipcart invoice number.
     *
     * https://www.shipstation.com/developer-api/#/reference/orders/list-orders/list-orders-with-parameters
     *
     * @param $orderNumber
     *
     * @return Order|null
     */
    public function getOrderByOrderNumber($orderNumber)
    {
        $response = $this->client->get('orders?orderNumber=' . $orderNumber);

        if ($response->getStatusCode() !== 200 && $response->getStatusCode() !== 201)
        {
            // something bad happened!
            Craft::warning('Failed to fetch ShipStation orders: ' . $response->getStatusCode());
            return null;
        }

        $responseData = json_decode($response->getBody(), true);

        if (count($responseData['orders']) === 1)
        {
            return new Order($responseData['orders'][0]);
        }

        return null;
    }

    /**
     * https://www.shipstation.com/developer-api/#/reference/orders/getdelete-order/get-order
     *
     * @param int $orderId
     *
     * @return Order|null
     */
    public function getOrder($orderId)
    {
        $response = $this->client->get("order/{$orderId}");

        if ($response->getStatusCode() !== 200 && $response->getStatusCode() !== 201)
        {
            // something bad happened!
            Craft::warning('Failed to fetch ShipStation order ' . $orderId. ': ' . $response->getStatusCode());
            return null;
        }

        $responseData = json_decode($response->getBody(), true);

        if ( ! empty($responseData))
        {
            return new Order($responseData);
        }

        return null;
    }

    /**
     * Get shipping rates based on the provided Snipcart order and package.
     *
     * @param SnipcartOrder   $order
     * @param SnipcartPackage $package
     *
     * @return ShippingRate[]
     */
    public function getRatesForSnipcartOrder(SnipcartOrder $order, SnipcartPackage $package): array
    {
        $rates  = [];
        $weight = new Weight([
            'value' => $order->totalWeight,
            'units' => Weight::UNIT_GRAMS,
        ]);

        if ($package !== null)
        {
            // translate Package into Dimensions
            $dimensions = new Dimensions();
            $dimensions->populateFromSnipcartPackage($package);

            if ( ! empty($package->weight))
            {
                // add the weight of the packaging if it's been specified
                $weight->value += $package->weight;
            }
        }

        /**
         * pass dimensions for rate quote if we have them,
         * otherwise just get the quote based on weight only
         */
        $shipStationRates = $this->getRates(
            $this->getToFromSnipcartOrder($order),
            $weight,
            isset($dimensions) && $dimensions->hasPhysicalDimensions() ? $dimensions : null
        );


        foreach ($shipStationRates as $shipStationRate)
        {
            $rates[] = new ShippingRate([
                'cost'        => number_format($shipStationRate->shipmentCost + $shipStationRate->otherCost, 2),
                'description' => $shipStationRate->serviceName,
                'code'        => $shipStationRate->serviceCode
            ]);
        }

        return $rates;
    }


    /**
     * Register a webhook subscription.
     *
     * @param $webhook
     */
    /*
    public function subscribeToWebhook($webhook)
    {
        // https://www.shipstation.com/developer-api/#/reference/webhooks/subscribe-to-webhook
    }

    public function listWebhooks()
    {

    }

    public function unsubscribeFromWebhook($webhookId)
    {
        // https://www.shipstation.com/developer-api/#/reference/webhooks/subscribe-to-webhook/unsubscribe-to-webhook
    }


    public function listCarrierServices($carrierCode)
    {

    }
    */

    /**
     * Send a Snipcart order to ShipStation.
     *
     * @param SnipcartOrder $snipcartOrder
     *
     * @return Order with ->id when successful, otherwise populated with errors.
     * @throws
     */
    public function sendSnipcartOrder(SnipcartOrder $snipcartOrder): Order
    {
        $order = new Order();

        $package = Snipcart::$plugin->orders->getOrderPackaging($snipcartOrder);

        $order->populateFromSnipcartOrder($snipcartOrder);

        $order->orderStatus   = Order::STATUS_AWAITING_SHIPMENT;
        $order->customerNotes = $this->getOrderNotes($snipcartOrder->customFields);
        $order->giftMessage   = $this->getGiftNote($snipcartOrder->customFields);
        $order->carrierCode   = $this->providerSettings['defaultCarrierCode'];

        // it's a gift order if it has a gift message
        $order->gift = $order->giftMessage !== null;

        $orderWeight = $snipcartOrder->totalWeight;

        if ( ! empty($package->weight))
        {
            $orderWeight += $package->weight; // add packing material weight
        }

        $order->weight = new Weight([
            'value' => $orderWeight,
            'units' => Weight::UNIT_GRAMS
        ]);

        $order->weight->validate();

        if ($package->hasPhysicalDimensions())
        {
            $order->dimensions = new Dimensions([
                'length' => $package->length,
                'width'  => $package->width,
                'height' => $package->height,
                'units'  => Dimensions::UNIT_INCHES,
            ]);

            $order->dimensions->validate();
        }

        if ($shippingMethod = $this->getShippingMethodFromOrder(
                $order,
                $snipcartOrder->shippingMethod
            )
        )
        {
            $order->serviceCode = $shippingMethod->serviceCode;
        }

        if ($order->validate())
        {
            if (Craft::$app->getConfig()->general->devMode)
            {
                // don't actually send orders to ShipStation in devMode, set a fake order ID
                $order->orderId = 99999999;
                return $order;
            }

            if ($createdOrder = $this->createOrder($order))
            {
                // TODO: delete related rate quotes when order makes it to ShipStation, or after a sensible amount of time
                return $createdOrder;
            }

            Craft::error('Failed to create ShipStation order for ' . $order->orderNumber);
            return $order;
        }

        // model has validation errors
        return $order;
    }


    // Private Methods
    // =========================================================================

    /**
     * Modify an array about to be sent via API to remove read-only fields that can't be set.
     *
     * @param $payload
     *
     * @return array
     */
    private function removeReadOnlyFieldsFromPayload($payload): array
    {
        // TODO: move this into a scenario on the model

        $removeIfNull = [
            'shipByDate',
            'customerId',
            'customerUsername',
            'internalNotes',
            'giftMessage',
            'paymentMethod',
            'packageCode',
            'confirmation',
            'shipDate',
            'holdUntilDate',
            'tagIds',
            'userId',
            'externallyFulfilledBy',
            'labelMessages',
            'insuranceOptions',
            'internationalOptions',
            'advancedOptions',
            'orderTotal',
        ];

        foreach ($removeIfNull as $removeKey)
        {
            if ($payload[$removeKey] === null)
            {
                unset($payload[$removeKey]);
            }
        }

        unset($payload['orderId'], $payload['createDate'], $payload['modifyDate'], $payload['externallyFulfilled']);

        foreach ($payload['items'] as &$item)
        {
            unset($item['orderItemId'], $item['adjustment'], $item['createDate'], $item['modifyDate']);
        }

        return $payload;
    }

    /**
     * Extract optional customer's note from a custom order comment field.
     *
     * @param array|null $customFields Custom fields data from Snipcart, an array of objects
     *
     * @return string|null
     */
    private function getOrderNotes($customFields)
    {
        if ( ! is_array($customFields))
        {
            return null;
        }

        foreach ($customFields as $customField)
        {
            if ($customField->name === $this->settings->orderCommentsFieldName)
            {
                return $customField->value;
            }
        }

        return null;
    }

    /**
     * Extract optional gift note from a custom order comment field.
     *
     * @param array|null $customFields Custom fields data from Snipcart, an array of objects
     *
     * @return string|null
     */
    private function getGiftNote($customFields)
    {
        if ( ! is_array($customFields))
        {
            return null;
        }

        $fieldName = Snipcart::$plugin->getSettings()->orderGiftNoteFieldName;

        foreach ($customFields as $customField)
        {
            if ($customField->name === $fieldName && ! empty($customField->value))
            {
                return $customField->value;
            }
        }

        return null;
    }

    /**
     * Translate Snipcart order data into to array for Shipstation, specifically
     * a rate request and not a normal Address model.
     *
     * @param SnipcartOrder $order
     * 
     * @return array
     */
    public function getToFromSnipcartOrder($order): array
    {
        return [
            'city'    => $order->shippingAddress->city,
            'state'   => $order->shippingAddress->province,
            'country' => $order->shippingAddress->country,
            'zip'     => $order->shippingAddress->postalCode,
        ];
    }

    /*
    private function convertGramsToOunces($grams)
    {
        return ceil($grams * 0.03527396);
    }
    */

    private function validateFrom($from): bool
    {
        return ! empty($from);
    }

    private function isLinked(): bool
    {
        return ! empty($this->providerSettings['apiKey']) &&
            ! empty($this->providerSettings['apiSecret']);
    }
}