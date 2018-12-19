<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\providers;

use workingconcept\snipcart\models\Order as SnipcartOrder;
use workingconcept\snipcart\models\shipstation\Order;
use workingconcept\snipcart\models\Package;
use workingconcept\snipcart\Snipcart;
use workingconcept\snipcart\models\shipstation\Weight;
use workingconcept\snipcart\models\shipstation\Dimensions;
use workingconcept\snipcart\models\shipstation\Rate;
use workingconcept\snipcart\models\ShippingRate as SnipcartRate;
use workingconcept\snipcart\records\ShippingQuoteLog;
use GuzzleHttp\Client;
use Craft;

class ShipStation extends ShippingProvider
{
    // Properties
    // =========================================================================

    /**
     * @var string ShipStation's base API URL used for all interactions.
     */
    protected static $apiBaseUrl = 'https://ssapi.shipstation.com/';
    protected $providerSettings;
    protected $client;


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $pluginSettings = Snipcart::$plugin->getSettings();
        $this->providerSettings = $pluginSettings->providers['shipStation'];
    }

    /**
     * @inheritdoc
     */
    public function isConfigured(): bool
    {
        return ! empty($this->providerSettings['apiKey']) &&
            ! empty($this->providerSettings['apiSecret']);
    }

    /**
     * @inheritdoc
     */
    public function getClient(): Client
    {
        if ($this->client !== null)
        {
            return $this->client;
        }

        $this->client = new Client([
            'base_uri' => self::$apiBaseUrl,
            'auth' => [
                $this->providerSettings['apiKey'],
                $this->providerSettings['apiSecret']
            ],
            'headers' => [
                'Content-Type' => 'application/json; charset=utf-8',
                'Accept'       => 'application/json',
            ],
            'verify' => false,
            'debug' => false
        ]);

        return $this->client;
    }

    /**
     * @inheritdoc
     */
    public function getRatesForOrder(SnipcartOrder $snipcartOrder, Package $package): array
    {
        $rates = [];
        $shipStationRates = $this->_getRatesForOrder($snipcartOrder, $package);


        /**
         * Convert response data into ShipStation Rates, then collect as
         * a Snipcart ShippingRate.
         */
        foreach ($shipStationRates as $responseItem)
        {
            $rate = new Rate($responseItem);

            $rates[] = new SnipcartRate([
                'cost'        => number_format($rate->shipmentCost + $rate->otherCost, 2),
                'description' => $rate->serviceName,
                'code'        => $rate->serviceCode
            ]);
        }

        return $rates;
    }

    /**
     * @inheritdoc
     */
    public function createOrder(SnipcartOrder $snipcartOrder)
    {
        $package = Snipcart::$plugin->orders->getOrderPackaging($snipcartOrder);
        $order = Order::populateFromSnipcartOrder($snipcartOrder);

        $order->orderStatus   = Order::STATUS_AWAITING_SHIPMENT;
        $order->customerNotes = $this->_getOrderNotes($snipcartOrder->customFields);
        $order->giftMessage   = $this->_getGiftNote($snipcartOrder->customFields);
        $order->carrierCode   = $this->providerSettings['defaultCarrierCode'];
        $order->weight        = $this->_getOrderWeight($snipcartOrder, $package);

        // it's a gift order if it has a gift message
        $order->gift = $order->giftMessage !== null;

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

        if ($shippingMethod = $this->_getShippingMethodFromOrder($snipcartOrder))
        {
            $order->serviceCode = $shippingMethod->serviceCode;
        }

        if ($order->validate())
        {
            if (Craft::$app->getConfig()->general->devMode)
            {
                /**
                 * Don't actually send orders to ShipStation in devMode,
                 * set a fake order ID.
                 */
                $order->orderId = 99999999;
                return $order;
            }

            if ($createdOrder = $this->_sendOrder($order))
            {
                /**
                 * TODO: delete related rate quotes when order makes it to
                 * ShipStation, or after a sensible amount of time
                 */
                return $createdOrder;
            }

            Craft::error(sprintf(
                'Failed to create ShipStation order for %s.',
                $order->orderNumber
            ));

            return $order;
        }

        // model has validation errors
        return $order;
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

        $response = $this->getClient()->post('orders/createlabelfororder', [
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
        $response = $this->getClient()->get(sprintf(
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
     * @param string $snipcartInvoice Snipcart invoice number
     *
     * @return Order|null
     */
    public function getOrderBySnipcartInvoice(string $snipcartInvoice)
    {
        $response = $this->getClient()->get('orders?orderNumber=' . $snipcartInvoice);

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
     * @param int $providerId ShipStation order ID
     *
     * @return Order|null
     */
    public function getOrderById($providerId)
    {
        $response = $this->getClient()->get("order/{$providerId}");

        if ($response->getStatusCode() !== 200 && $response->getStatusCode() !== 201)
        {
            // something bad happened!
            Craft::warning('Failed to fetch ShipStation order ' . $providerId. ': ' . $response->getStatusCode());
            return null;
        }

        $responseData = json_decode($response->getBody(), true);

        if ( ! empty($responseData))
        {
            return new Order($responseData);
        }

        return null;
    }


    // Private Methods
    // =========================================================================

    /**
     * Get an array of shipment information for requesting a rate quote.
     *
     * @param SnipcartOrder $snipcartOrder
     * @param Dimensions $dimensions
     * @param Weight $weight
     * @return array
     */
    private function _prepShipmentInfo(
        SnipcartOrder $snipcartOrder,
        Dimensions $dimensions,
        Weight $weight
    ): array
    {
        $pluginSettings = Snipcart::$plugin->getSettings();

        $shipmentInfo = [
            'carrierCode'    => $this->providerSettings['defaultCarrierCode'],
            //'serviceCode'  => '',
            'packageCode'    => $this->providerSettings['defaultPackageCode'],
            'fromPostalCode' => $pluginSettings->shipFromAddress['postalCode'],
            'toCity'         => $snipcartOrder->shippingAddress->city,
            'toState'        => $snipcartOrder->shippingAddress->province,
            'toPostalCode'   => $snipcartOrder->shippingAddress->postalCode,
            'toCountry'      => $snipcartOrder->shippingAddress->country,
            'weight'         => $weight->toArray(),
            'confirmation'   => $this->providerSettings['defaultOrderConfirmation'],
            'residential'    => false
        ];

        if ($dimensions->hasPhysicalDimensions())
        {
            $shipmentInfo['dimensions'] = $dimensions->toArray();
        }

        return $shipmentInfo;
    }

    /**
     * Send the order to ShipStation via API.
     *
     * @param Order $order
     * @return Order|null
     */
    private function _sendOrder(Order $order)
    {
        $response = $this->getClient()->post('orders/createorder', [
            \GuzzleHttp\RequestOptions::JSON => $order->getPayloadForPost()
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
     * Get a Weight model for the order, adding package weight when relevant.
     *
     * @param SnipcartOrder $snipcartOrder
     * @param Package $package
     *
     * @return Weight
     */
    private function _getOrderWeight(SnipcartOrder $snipcartOrder, Package $package): Weight
    {
        $orderWeight = $snipcartOrder->totalWeight;

        if ( ! empty($package->weight))
        {
            $orderWeight += $package->weight; // add packing material weight
        }

        $weight = new Weight([
            'value' => $orderWeight,
            'units' => Weight::UNIT_GRAMS
        ]);

        $weight->validate();

        return $weight;
    }

    /**
     * Extract the value from a specific custom field, if it exists.
     *
     * @param array|null $customFields Custom fields data from Snipcart,
     *                                 an array of objects
     * @param string     $fieldName    Name of the field as seen in the order.
     *
     * @return string|null
     */
    private function _getValueFromCustomFields($customFields, $fieldName)
    {
        if ( ! is_array($customFields))
        {
            return null;
        }

        foreach ($customFields as $customField)
        {
            if ($customField->name === $fieldName)
            {
                return $customField->value;
            }
        }

        return null;
    }

    /**
     * Extract optional customer's note from a custom order comment field.
     *
     * @param array|null $customFields Custom fields data from Snipcart,
     *                                 an array of objects
     *
     * @return string|null
     */
    private function _getOrderNotes($customFields)
    {
        $fieldName = Snipcart::$plugin->getSettings()->orderCommentsFieldName;

        return $this->_getValueFromCustomFields($customFields, $fieldName);
    }

    /**
     * Extract optional gift note from a custom order comment field.
     *
     * @param array|null $customFields Custom fields data from Snipcart,
     *                                 an array of objects
     *
     * @return string|null
     */
    private function _getGiftNote($customFields)
    {
        $fieldName = Snipcart::$plugin->getSettings()->orderGiftNoteFieldName;

        return $this->_getValueFromCustomFields($customFields, $fieldName);
    }

    /**
     * Return ShipStation rates for a Snipcart order.
     *
     * @param SnipcartOrder $snipcartOrder
     * @param Package $package
     * @return Rate[]
     */
    private function _getRatesForOrder(SnipcartOrder $snipcartOrder, Package $package): array
    {
        $rates        = [];
        $dimensions   = Dimensions::populateFromSnipcartPackage($package);
        $weight       = $this->_getOrderWeight($snipcartOrder, $package);
        $shipmentInfo = $this->_prepShipmentInfo(
            $snipcartOrder,
            $dimensions,
            $weight
        );

        try
        {
            $response = $this->getClient()->post('shipments/getrates', [
                \GuzzleHttp\RequestOptions::JSON => $shipmentInfo
            ]);

            $responseData = json_decode($response->getBody());
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

            return [];
        }

        foreach ($responseData as $responseItem)
        {
            $rates[] = new Rate($responseItem);
        }

        return $rates;
    }

    /**
     * Attempt to figure out which ShipStation Rate the customer chose after
     * the order was placed. We do this since there's no way to consistently
     * reference an order before and after completion.
     *
     * @todo Or can we? Does an order have a unique ID beyond its invoice number?
     *
     * @param SnipcartOrder  $order           Snipcart order.
     * @return Rate|null
     */
    private function _getShippingMethodFromOrder(SnipcartOrder $order)
    {
        $closest = null;

        /**
         * First try and find a matching rate quote, which would have preceded
         * the completed order.
         */
        $rateQuote = ShippingQuoteLog::find()
            ->where(['token' => $order->token])
            ->orderBy(['dateCreated' => SORT_DESC])
            ->one();

        if ( ! empty($rateQuote))
        {
            // get the rates that were already returned to Snipcart earlier
            $quoteRecord = json_decode($rateQuote->body);

            foreach ($quoteRecord->rates as $quotedRate)
            {
                /**
                 * See if the collected shipping fees and service name are
                 * an exact match.
                 */
                $labelAndCostMatch = $quotedRate->description === $order->shippingMethod
                    && (float)$quotedRate->cost === $order->shippingFees;

                if ($labelAndCostMatch)
                {
                    return new Rate([
                        'serviceName'  => $quotedRate->description,
                        'serviceCode'  => $quotedRate->code,
                        'shipmentCost' => $quotedRate->cost,
                        'otherCost'    => 0,
                    ]);
                }
            }
        }

        /**
         * If there wasn't a matching option, query the API for rates again
         * and look for the closest match.
         */
        $package = Snipcart::$plugin->orders->getOrderPackaging($order);
        $rates   = $this->_getRatesForOrder($order, $package);

        // check rates for matching name and/or price, otherwise take closest
        foreach ($rates as $rate)
        {
            /**
             * See if the collected shipping fees and service name are
             * an exact match.
             */
            $labelAndCostMatch = $rate->serviceName === $order->shippingMethod
                && ($rate->shipmentCost + $rate->otherCost) === $order->shippingFees;

            if ($labelAndCostMatch)
            {
                // return exact match
                return $rate;
            }

            if ($closest === null)
            {
                $closest = $rate;
                continue;
            }

            $currentRateDelta = abs($rate->shipmentCost - $order->shippingFees);
            $closestRateDelta = abs($closest->shipmentCost - $order->shippingFees);

            if ($currentRateDelta < $closestRateDelta)
            {
                // use the rate that has the least cost difference
                $closest = $rate;
            }
        }

        return $closest;
    }


}
