<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\services;

use workingconcept\snipcart\models\ShipStationAddress;
use workingconcept\snipcart\models\ShipStationOrderItem;
use workingconcept\snipcart\models\ShipStationDimensions;
use workingconcept\snipcart\models\ShipStationOrder;
use workingconcept\snipcart\models\ShipStationWeight;
use workingconcept\snipcart\models\ShipStationItemOption;
use workingconcept\snipcart\Snipcart;
use workingconcept\snipcart\records\ShippingQuoteLog;
use workingconcept\snipcart\models\SnipcartOrder;

use Craft;
use craft\base\Component;

class ShipStationService extends Component
{

    // Constants
    // =========================================================================

    const API_BASE_URL = 'https://ssapi.shipstation.com/';


    // Properties
    // =========================================================================

    protected $client;
    protected $shipFrom;
    protected $settings;
    protected $providerSettings;

    protected $webhookOptions = [
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
            $this->client = new \GuzzleHttp\Client([
                'base_uri' => self::API_BASE_URL,
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
            throw new \Exception('Please add ShipStation API key and secret.');
        }
    }


    /**
     * Get shipping rates for the supplied order details.
     * https://www.shipstation.com/developer-api/#/reference/shipments/get-rates
     *
     * @param array                  $to [
     *                          		'city' => 'Seattle',
     *                          		'state' => 'WA',
     *                          		'country' => 'US'
     *                          		'zip' => '98103'
     *                         	     ]
     * @param ShipStationWeight      $weight
     * @param ShipStationDimensions  $dimensions (optional)
     * @param array                  $from (optional; defaults to standard shipFrom)
     * 
     * @return StdClass decoded response data
     * @throws Exception
     */
    public function getRates($to, ShipStationWeight $weight, ShipStationDimensions $dimensions = null, $from = [])
    {
        if ($shipFrom = $this->validateFrom($from))
        {
            $shipFrom = $from;
        }
        else
        {
            $shipFrom = $this->shipFrom;
        }

        $shipmentInfo = [
            'carrierCode'    => $this->providerSettings['defaultCarrierCode'],
            //'serviceCode'  => '',
            'packageCode'    => $this->providerSettings['defaultPackageCode'],
            'fromPostalCode' => $shipFrom['postalCode'],
            'toPostalCode'   => $to['zip'],
            'toCity'         => $to['city'],
            'toState'        => $to['state'], // two-character state/province abbreviation
            'toCountry'      => $to['country'], // two-character ISO country code
            'weight'         => $weight->toArray(),
            'confirmation'   => $this->providerSettings['defaultOrderConfirmation'],
            'residential'    => false
        ];

        if ( ! is_null($dimensions))
        {
            $shipmentInfo['dimensions'] = $dimensions->toArray();
        }

        try
        {
            $response = $this->client->post('shipments/getrates', [
                \GuzzleHttp\RequestOptions::JSON => $shipmentInfo
            ]);
        }
        catch(\GuzzleHttp\Exception\ServerException $e)
        {
            // ShipStation returns a 500 error with a message if there aren't any service options
            //Craft::dd($response);
            Craft::error($e, 'snipcart');

            if (class_exists('\superbig\bugsnag\Bugsnag'))
            {
                \superbig\bugsnag\Bugsnag::$plugin->bugsnagService->handleException($e);
            }

            // return empty array
            return [];
        }

        $responseData = json_decode($response->getBody(true));

        return $responseData;
    }


    /**
     * Get shipping rates for the supplied order details, and filter out ones we don't want to show.
     * https://www.shipstation.com/developer-api/#/reference/shipments/get-rates
     *
     * @param array                  $to [
     *                          		'city' => 'Seattle',
     *                          		'state' => 'WA',
     *                          		'country' => 'US'
     *                          		'zip' => '98103'
     *                         	     ]
     * @param ShipStationWeight      $weight
     * @param ShipStationDimensions  $dimensions
     * @param array                  $items
     *
     * @return StdClass decoded response data
     * @throws Exception
     */
    public function getFilteredRates($to, ShipStationWeight $weight, ShipStationDimensions $dimensions = null, $items = [])
    {
        $rates = $this->getRates($to, $weight, $dimensions);

        return $this->filterRates($rates, $weight, $dimensions, $items);
    }


    /**
     * Create an order.
     * https://shipstation.docs.apiary.io/#reference/orders/createupdate-order/create/update-order
     *
     * @param ShipStationOrder $order
     *
     * @return StdClass decoded response data, with ->labelData base64-encoded PDF body
     * @throws Exception
     */
    public function createOrder(ShipStationOrder $order)
    {
        $payload = $order->toArray([], $order->extraFields(), true);
        $payload = $this->removeReadOnlyFieldsFromPayload($payload);

        $response = $this->client->post('orders/createorder', [
            \GuzzleHttp\RequestOptions::JSON => $payload
        ]);

        if ($response->getStatusCode() !== 200)
        {
            // something bad happened!
            return;
        }

        $responseData = json_decode($response->getBody(true), true);

        return $this->populateModelFromResponseData($responseData);
    }


    /**
     * Attempt to figure out which shipping method the customer chose.
     *
     * @param ShipStationOrder $order           Order in progress in response to Snipcart data.
     * @param string           $shippingMethod  Formatted shipping name selected by customer and provided
     *                                          by Snipcart, to be used as a clue.
     * @return array|null      [ 
     *                          'serviceName' => 'USPS Priority Mail - Package',
     *                          'serviceCode' => 'usps_priority_mail',
     *                          'shipmentCost' => 6.98,
     *                          'otherCost' => 0
     *                         ]
     */
    public function getShippingMethodFromOrder(ShipStationOrder $order, $shippingMethod)
    {
        $closest = null;

        /**
         * first try and find a matching rate quote, which would have preceded the completed order
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
                if ((float)$rate->cost === $order->shippingAmount && $rate->description === $order->requestedShippingService)
                {
                    return (object) [
                        'serviceName'  => $rate->description,
                        'serviceCode'  => $rate->code,
                        'shipmentCost' => $rate->cost,
                        'otherCost'    => 0,
                    ];
                }
            }
        }

        /**
         * if there wasn't a matching option, get rate quote again to find closest possible match
         */

        // check rates again to get potential choices
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
            if ($rate->serviceName == $shippingMethod && $rate->shipmentCost == $order->shippingAmount)
            {
                // return exact match
                return $rate;
            }
            else
            {
                if (
                    $closest === null ||
                    abs($rate->shipmentCost - $order->shippingAmount) < abs($closest->shipmentCost - $order->shippingAmount)
                )
                {
                    // store the rate that has the least cost difference
                    $closest = $rate;
                }
            }
        }

        return $closest;
    }


    /**
     * Create a label for an order, which will allow it to associate with order details
     * and populate a packing slip.
     *
     * Identical to createShipmentLabel() except for the required orderId.
     *
     *
     * https://shipstation.docs.apiary.io/#reference/orders/create-label-for-order/create-label-for-order
     *
     * @param ShipStationOrder  $order
     * @param array             $isTest  true if we only want to create a sample label
     *
     * @return StdClass decoded response data, with ->labelData base64-encoded PDF body
     * @throws Exception
     */
    public function createLabelForOrder(ShipStationOrder $order, $isTest = false, $packageCode = 'package')
    {
        $payload = $order->toArray([], $order->extraFields(), true);

        $payload['testLabel'] = $isTest;
        $payload['packageCode'] = $packageCode; // TODO: do something serious here

        $response = $this->client->post('orders/createlabelfororder', [
            \GuzzleHttp\RequestOptions::JSON => $payload
        ]);

        if ($response->getStatusCode() !== 200)
        {
            // something bad happened!
            return;
        }

        $responseData = json_decode($response->getBody(true));

        return $responseData;
    }


    /**
     * https://www.shipstation.com/developer-api/#/reference/orders/list-orders/list-orders-w/o-parameters
     *
     * @return void
     */
    public function listOrders($limit = 25)
    {
        $response = $this->client->get('orders?pageSize=' . $limit . '&sortBy=OrderDate&sortDir=DESC');
        $orders = [];

        if ($response->getStatusCode() !== 200)
        {
            // something bad happened!
            return;
        }

        $responseData = json_decode($response->getBody(true), true);

        foreach ($responseData['orders'] as $order)
        {
            $orders[] = $this->populateModelFromResponseData($order);
        }

        return $orders;
    }

    /**
     * Register a webhook subscription. (NOT IMPLEMENTED!)
     *
     * @param $webhook
     */

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


    /**
     * Send a Snipcart order to ShipStation.
     *
     * @param SnipcartOrder $snipcartOrder
     *
     * @return array|bool
     */
    public function sendSnipCartOrder(SnipcartOrder $snipcartOrder)
    {
        $shipstationOrder = new ShipStationOrder();

        $packageDetails = Snipcart::$plugin->snipcart->getOrderPackagingDetails($snipcartOrder);

        $shipstationOrder->setAttributes([
            'orderNumber'              => $snipcartOrder->invoiceNumber,
            'orderKey'                 => $snipcartOrder->token,
            'serviceCode'              => null, // to be updated below
            'carrierCode'              => $this->providerSettings['defaultCarrierCode'],
            'orderDate'                => $snipcartOrder->creationDate,
            'paymentDate'              => $snipcartOrder->creationDate,
            'customerEmail'            => $snipcartOrder->email,
            'amountPaid'               => $snipcartOrder->total,
            'shippingAmount'           => $snipcartOrder->shippingFees,
            'requestedShippingService' => $snipcartOrder->shippingMethod,
            'customerNotes'            => $this->getOrderNotesFromCustomFields($snipcartOrder->customFields),
            'giftMessage'              => $this->getGiftNoteFromCustomFields($snipcartOrder->customFields),
            'orderStatus'              => ShipStationOrder::STATUS_AWAITING_SHIPMENT
        ]);

        if ( ! empty($snipcartOrder->giftMessage))
        {
            $shipstationOrder->gift = true;
        }

        $shipstationOrder->shipTo = new ShipStationAddress([
            'name'       => $snipcartOrder->shippingAddressName,
            'street1'    => $snipcartOrder->shippingAddressAddress1,
            'street2'    => $snipcartOrder->shippingAddressAddress2,
            'city'       => $snipcartOrder->shippingAddressCity,
            'state'      => $snipcartOrder->shippingAddressProvince,
            'postalCode' => $snipcartOrder->shippingAddressPostalCode,
            'phone'      => $snipcartOrder->shippingAddressPhone
        ]);

        $shipstationOrder->shipTo->validate();

        $shipstationOrder->billTo = new ShipStationAddress([
            'name'       => $snipcartOrder->billingAddressName,
            'street1'    => $snipcartOrder->billingAddressAddress1,
            'street2'    => $snipcartOrder->billingAddressAddress2,
            'city'       => $snipcartOrder->billingAddressCity,
            'state'      => $snipcartOrder->billingAddressProvince,
            'postalCode' => $snipcartOrder->billingAddressPostalCode,
            'phone'      => $snipcartOrder->billingAddressPhone
        ]);

        $shipstationOrder->billTo->validate();

        $orderWeight = $snipcartOrder->totalWeight;

        if ( ! empty($packageDetails['weight']))
        {
            // add the weight of packing materials
            $orderWeight += $packageDetails['weight'];
        }

        $shipstationOrder->weight = new ShipStationWeight([
            'value' => $orderWeight,
            'units' => ShipStationWeight::UNIT_GRAMS
        ]);

        $shipstationOrder->weight->validate();

        if ( ! empty($packageDetails['length']) && ! empty($packageDetails['width']) && ! empty($packageDetails['height']))
        {
            $shipstationOrder->dimensions = new ShipStationDimensions([
                'length' => $packageDetails['length'],
                'width'  => $packageDetails['width'],
                'height' => $packageDetails['height'],
                'units'  => ShipStationDimensions::UNIT_INCHES,
            ]);

            $shipstationOrder->dimensions->validate();
        }

        $orderItems = [];

        foreach ($snipcartOrder->items as $item)
        {
            $orderItem = new ShipStationOrderItem();

            $orderItem->setAttributes([
                'lineItemKey' => $item->id,
                'name'        => $item->name,
                'quantity'    => $item->quantity,
                'unitPrice'   => $item->price,
            ]);

            $itemWeight = new ShipStationWeight();
            $itemWeight->setAttributes([
                'value' => $item->weight,
                'units' => ShipStationWeight::UNIT_GRAMS,
            ]);
            $itemWeight->validate();

            $orderItem->weight = $itemWeight;

            if ( ! empty($item->customFields))
            {
                $itemOptions = [];

                foreach ($item->customFields as $customField)
                {
                    $itemOption = new ShipStationItemOption();

                    $itemOption->name  = $customField->name;
                    $itemOption->value = $customField->value;
                    $itemOption->validate();

                    $itemOptions[] = $itemOption;
                }

                $orderItem->setOptions($itemOptions);
            }

            $orderItem->validate();

            $orderItems[] = $orderItem;
        }

        $shipstationOrder->items = $orderItems;

        if ($shippingMethod = Snipcart::$plugin->shipStation->getShippingMethodFromOrder($shipstationOrder, $snipcartOrder->shippingMethod))
        {
            $shipstationOrder->serviceCode = $shippingMethod->serviceCode;
        }

        if ($shipstationOrder->validate())
        {
            if (Craft::$app->getConfig()->general->devMode)
            {
                // don't actually send orders to ShipStation in devMode
                // TODO: simulate created order
                return;
            }

            if ($createdOrder = Snipcart::$plugin->shipStation->createOrder($shipstationOrder))
            {
                // TODO: delete related rate quotes when order makes it to ShipStation
                return $createdOrder;
            }
            else
            {
                if (class_exists('\superbig\bugsnag\Bugsnag'))
                {
                    \superbig\bugsnag\Bugsnag::$plugin->bugsnagService->handleException('Order not created.');
                }

                return false;
            }
        }
        else
        {
            return $snipcartOrder->getErrors();
        }
    }


    // Private Methods
    // =========================================================================


    /**
     * Build a new ShipStationOrder using data we got back from the ShipStation API.
     *
     * @param $data  associative array of API response data
     * @return ShipStationOrder
     */
    private function populateModelFromResponseData($data)
    {
        $order = new ShipStationOrder();
        $order->attributes = $data;

        $order->billTo     = new ShipStationAddress($data['billTo']);
        $order->shipTo     = new ShipStationAddress($data['shipTo']);
        $order->weight     = new ShipStationWeight($data['weight']);
        $order->dimensions = new ShipStationDimensions($data['dimensions']);

        // TODO: figure out how to get these working
        //$order->insuranceOptions = new ShipStationInsuranceOptions($data['insuranceOptions']);
        //$order->internationalOptions = new ShipStationInternationalOptions($data['insuranceOptions']);

        $orderItems = [];

        foreach ($data['items'] as $item)
        {
            $newItem = new ShipStationOrderItem($item);
            $newOptions = [];

            foreach	($item['options'] as $option)
            {
                $newOptions[] = new ShipStationItemOption($option);
            }

            $newItem->weight  = new ShipStationWeight($item['weight']);
            $newItem->options = $newOptions;

            $orderItems[] = $newItem;
        }

        $order->items = $orderItems;

        return $order;
    }


    /**
     * Modify an array about to be sent via API to remove read-only fields that can't be set.
     *
     * @param $payload
     *
     * @return array
     */
    private function removeReadOnlyFieldsFromPayload($payload)
    {
        unset($payload['orderId']);

        foreach($payload['items'] as &$item)
        {
            unset($item['orderItemId']);
            unset($item['adjustment']);
            unset($item['createDate']);
            unset($item['modifyDate']);
        }

        return $payload;
    }


    /**
     * Extract optional customer's note from a custom order comment field.
     *
     * @param array $customFields Custom fields data from Snipcart, an array of objects
     *
     * @return string|null
     */
    private function getOrderNotesFromCustomFields($customFields)
    {
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
     * @param array $customFields Custom fields data from Snipcart, an array of objects
     *
     * @return string|null
     */
    private function getGiftNoteFromCustomFields($customFields)
    {
        foreach ($customFields as $customField)
        {
            if ($customField->name === $this->settings->orderGiftNoteFieldName)
            {
                return $customField->value;
            }
        }

        return null;
    }


    /**
     * Translate Snipcart order data into to array for Shipstation.
     *
     * @param SnipcartOrder $order
     * 
     * @return array
     */
    public function getToFromSnipcartData($order)
    {
        return [
            'city'    => $order->shippingAddressCity,
            'state'   => $order->shippingAddressProvince,
            'country' => $order->shippingAddressCountry,
            'zip'     => $order->shippingAddressPostalCode,
        ];
    }


    /**
     * Translate Snipcart order data into to array for Shipstation.
     *
     * @param SnipcartOrder $order
     *
     * @return ShipStationWeight
     */
    public function getWeightFromSnipcartData($order)
    {
        return new ShipStationWeight([
            'value' => $order->totalWeight,
            'units' => ShipStationWeight::UNIT_GRAMS,
        ]);
    }


    /**
     * Translate Snipcart order data into to array for Shipstation.
     *
     * @param array $packageDetails
     *
     * @return ShipStationDimensions
     */
    public function getDimensionsFromSnipcartData($packageDetails)
    {
        return new ShipStationDimensions([
            'length' => $packageDetails['length'],
            'width'  => $packageDetails['width'],
            'height' => $packageDetails['height'],
            'units'  => ShipStationDimensions::UNIT_INCHES
        ]);
    }

    private function convertGramsToOunces($grams)
    {
        return ceil($grams * 0.03527396);
    }

    private function validateFrom($from)
    {
        return ! empty($from);
    }

    private function isLinked()
    {
        return ! empty($this->providerSettings['apiKey']) &&
            ! empty($this->providerSettings['apiSecret']);
    }
}