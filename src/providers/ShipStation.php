<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\providers;

use Craft;
use GuzzleHttp\Client;
use workingconcept\snipcart\models\Order as SnipcartOrder;
use workingconcept\snipcart\models\Package;
use workingconcept\snipcart\models\ShippingRate as SnipcartRate;
use workingconcept\snipcart\models\shipstation\Dimensions;
use workingconcept\snipcart\models\shipstation\Order;
use workingconcept\snipcart\models\shipstation\Rate;
use workingconcept\snipcart\models\shipstation\Weight;
use workingconcept\snipcart\records\ShippingQuoteLog;
use workingconcept\snipcart\Snipcart;
use workingconcept\snipcart\helpers\ModelHelper;
use workingconcept\snipcart\base\ShippingProvider;

/**
 * Class ShipStation
 *
 * @package workingconcept\snipcart\providers
 * @todo log exceptions for troubleshooting
 */
class ShipStation extends ShippingProvider
{
    // Properties
    // =========================================================================

    /**
     * @var Client
     */
    protected $client;


    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function refHandle(): string
    {
        return 'shipStation';
    }

    /**
     * @inheritdoc
     */
    public static function getApiBaseUrl(): string
    {
        return 'https://ssapi.shipstation.com/';
    }


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $pluginSettings = Snipcart::$plugin->getSettings();
        $this->providerSettings = $pluginSettings->providers[self::refHandle()] ?? null;
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
            'base_uri' => self::getApiBaseUrl(),
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

        /**
         * Convert response data into ShipStation Rates, then collect as
         * a Snipcart ShippingRate.
         */
        foreach ($this->_getRatesForOrder($snipcartOrder, $package) as $responseItem)
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
        $order   = Order::populateFromSnipcartOrder($snipcartOrder);

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
                 * Don't transmit orders in devMode, just set a fake order ID.
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

        return $this->post('orders/createlabelfororder', $payload);
    }

    /**
     * @inheritdoc
     * https://www.shipstation.com/developer-api/#/reference/orders/getdelete-order/get-order
     */
    public function getOrderById($providerId)
    {
        $responseData = $this->get(sprintf(
            'order/%d',
            $providerId
        ));

        if ( ! empty($responseData))
        {
            return new Order($responseData);
        }

        return null;
    }

    /**
     * @inheritdoc
     * https://www.shipstation.com/developer-api/#/reference/orders/list-orders/list-orders-with-parameters
     */
    public function getOrderBySnipcartInvoice(string $snipcartInvoice)
    {
        $responseData = $this->get(sprintf(
            'orders?orderNumber=%s',
            $snipcartInvoice
        ));

        if (count($responseData->orders) === 1)
        {
            return new Order($responseData->orders[0]);
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
        $responseData = $this->post(
            'orders/createorder',
            $order->getPayloadForPost()
        );

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
     * Extract optional customer's note from a custom order comment field.
     *
     * @param array|null $customFields Custom fields data from Snipcart,
     *                                 an array of objects
     *
     * @return string|null
     */
    private function _getOrderNotes($customFields)
    {
        return $this->getValueFromCustomFields(
            $customFields,
            Snipcart::$plugin->getSettings()->orderCommentsFieldName,
            true
        );
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
        return $this->getValueFromCustomFields(
            $customFields,
            Snipcart::$plugin->getSettings()->orderGiftNoteFieldName,
            true
        );
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
        $dimensions   = Dimensions::populateFromSnipcartPackage($package);
        $weight       = $this->_getOrderWeight($snipcartOrder, $package);
        $shipmentInfo = $this->_prepShipmentInfo(
            $snipcartOrder,
            $dimensions,
            $weight
        );

        $responseData = $this->post(
            'shipments/getrates',
            $shipmentInfo
        );

        if ($responseData === null)
        {
            Craft::info(sprintf(
                'ShipStation did not return any rates for %s',
                $snipcartOrder->invoiceNumber
            ), 'snipcart');

            return [];
        }

        return ModelHelper::populateArrayWithModels(
            $responseData,
            Rate::class
        );
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
        $rateQuote = Snipcart::$plugin->shipments->getQuoteLogForOrder($order);

        if ( ! empty($rateQuote))
        {
            $rate = $this->_getMatchingRateFromLog($rateQuote, $order);

            if ($rate !== null)
            {
                return $rate;
            }
        }

        /**
         * If there wasn't a matching option, query the API for rates again
         * and look for the closest match.
         */
        return $this->_getClosestRateForOrder($order);
    }

    /**
     * Check logged rates and return whichever previously-shown rate exactly
     * matches the rate cost and description for this order.
     *
     * @param $rateQuoteLog
     * @return Rate|null
     */
    private function _getMatchingRateFromLog($rateQuoteLog, $order)
    {
        // get the rates that were already returned to Snipcart earlier
        $quoteRecord = json_decode($rateQuoteLog->body);

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

        return null;
    }

    /**
     * Fetch new rates based on this order, and choose either the exact match
     * or next-closest rate to what was purchased and specified at checkout.
     *
     * @param $order
     * @return Rate|null
     */
    private function _getClosestRateForOrder($order)
    {
        $closest = null;
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
