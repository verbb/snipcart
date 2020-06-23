<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\providers\shipstation;

use Craft;
use craft\helpers\Json;
use workingconcept\snipcart\helpers\VersionHelper;
use workingconcept\snipcart\models\snipcart\Order as SnipcartOrder;
use workingconcept\snipcart\models\snipcart\Package;
use workingconcept\snipcart\models\snipcart\ShippingRate as SnipcartRate;
use workingconcept\snipcart\models\shipstation\Dimensions;
use workingconcept\snipcart\models\shipstation\Order;
use workingconcept\snipcart\models\shipstation\Rate;
use workingconcept\snipcart\models\shipstation\Weight;
use workingconcept\snipcart\Snipcart;
use workingconcept\snipcart\helpers\ModelHelper;
use workingconcept\snipcart\base\ShippingProvider;
use workingconcept\snipcart\providers\shipstation\Settings as ShipStationSettings;
use workingconcept\snipcart\providers\shipstation\events\OrderEvent;

/**
 * Class ShipStation
 *
 * @package workingconcept\snipcart\providers
 * @todo log exceptions for troubleshooting
 */
class ShipStation extends ShippingProvider
{
    /**
     * @event WebhookEvent Triggered before an order is sent to ShipStation.
     */
    const EVENT_BEFORE_SEND_ORDER = 'beforeSendOrder';

    /**
     * @var int  fake ID returned to simulate success in test mode
     */
    const TEST_ORDER_ID = 99999999;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

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
    public static function apiBaseUrl(): string
    {
        return 'https://ssapi.shipstation.com/';
    }

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new ShipStationSettings();
    }

    /**
     * @inheritdoc
     */
    public function isConfigured(): bool
    {
        if ($settings = $this->getSettings()) {
            return ! empty($settings->apiKey) &&
                ! empty($settings->apiSecret) &&
                ! empty($settings->defaultCountry) &&
                ! empty($settings->defaultOrderConfirmation) &&
                ! empty($settings->defaultWarehouseId);
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function getClient(): \GuzzleHttp\Client
    {
        if ($this->client !== null) {
            return $this->client;
        }

        $key = $this->getSettings()->apiKey;
        $secret = $this->getSettings()->apiSecret;

        if (VersionHelper::isCraft31()) {
            $key = Craft::parseEnv($key);
            $secret = Craft::parseEnv($secret);
        }

        $clientConfig = [
            'base_uri' => self::apiBaseUrl(),
            'auth' => [ $key, $secret ],
            'headers' => [
                'Content-Type' => 'application/json; charset=utf-8',
                'Accept'       => 'application/json',
            ],
            'verify' => false,
            'debug' => false
        ];

        $this->client = Craft::createGuzzleClient($clientConfig);

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
        foreach ($this->getShipStationRatesForOrder($snipcartOrder, $package) as $responseItem) {
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
     * https://www.shipstation.com/docs/api/orders/create-update-order/
     * @inheritdoc
     */
    public function createOrder(SnipcartOrder $snipcartOrder)
    {
        $package = Snipcart::$plugin->orders->getOrderPackaging($snipcartOrder);
        $order   = Order::populateFromSnipcartOrder($snipcartOrder);

        $order->orderStatus   = Order::STATUS_AWAITING_SHIPMENT;
        $order->customerNotes = $this->getOrderNotes($snipcartOrder->customFields);
        $order->giftMessage   = $this->getGiftNote($snipcartOrder->customFields);
        $order->weight        = $this->getOrderWeight($snipcartOrder, $package);

        // it's a gift order if it has a gift message
        $order->gift = $order->giftMessage !== null;

        if ($package->hasPhysicalDimensions()) {
            $order->dimensions = new Dimensions([
                'length' => $package->length,
                'width'  => $package->width,
                'height' => $package->height,
                'units'  => Dimensions::UNIT_INCHES,
            ]);

            $order->dimensions->validate();
        }

        if (($shippingMethod = $this->getShippingMethodFromOrder($snipcartOrder)) &&
            ! empty($shippingMethod->serviceCode)
        ) {
            $order->carrierCode = $this->getSettings()->defaultCarrierCode;
            $order->serviceCode = $shippingMethod->serviceCode;
        }

        if ($order->validate()) {
            $isDevMode = Craft::$app->getConfig()->general->devMode;
            $isTestMode = Snipcart::$plugin->getSettings()->testMode;

            if ($this->hasEventHandlers(self::EVENT_BEFORE_SEND_ORDER)) {
                $event = new OrderEvent([
                    'order' => $order,
                ]);

                $this->trigger(self::EVENT_BEFORE_SEND_ORDER, $event);

                $order = $event->order;
            }

            if ($isDevMode || $isTestMode) {
                /**
                 * Don't transmit orders in devMode or testMode, just set a fake order ID.
                 */
                $order->orderId = self::TEST_ORDER_ID;
                return $order;
            }

            if ($createdOrder = $this->sendOrder($order)) {
                /**
                 * TODO: delete related rate quotes when order makes it to
                 * ShipStation, or after a sensible amount of time
                 */
                return $createdOrder;
            }

            Craft::error(sprintf(
                'Failed to create ShipStation order for %s.',
                $order->orderNumber
            ), 'snipcart');

            return $order;
        }

        // model has validation errors
        return $order;
    }

    /**
     * Creates a label for an order, which will allow it to associate with order
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

        if (! empty($responseData)) {
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

        if (count($responseData->orders) === 1) {
            return new Order($responseData->orders[0]);
        }

        return null;
    }

    /**
     * Gets an array of shipment information for requesting a rate quote.
     *
     * @param SnipcartOrder $snipcartOrder
     * @param Dimensions $dimensions
     * @param Weight $weight
     * @return array
     */
    private function prepShipmentInfo(
        SnipcartOrder $snipcartOrder,
        Dimensions $dimensions,
        Weight $weight
    ): array {
        $pluginSettings = Snipcart::$plugin->getSettings();

        $shipmentInfo = [
            'carrierCode'    => $this->getSettings()->defaultCarrierCode,
            //'serviceCode'  => '',
            'packageCode'    => $this->getSettings()->defaultPackageCode,
            'fromPostalCode' => $pluginSettings->shipFromAddress['postalCode'],
            'toCity'         => $snipcartOrder->shippingAddress->city,
            'toState'        => $snipcartOrder->shippingAddress->province,
            'toPostalCode'   => $snipcartOrder->shippingAddress->postalCode,
            'toCountry'      => $snipcartOrder->shippingAddress->country,
            'weight'         => $weight->toArray(),
            'confirmation'   => $this->getSettings()->defaultOrderConfirmation,
            'residential'    => false
        ];

        if ($dimensions->hasPhysicalDimensions()) {
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
    private function sendOrder(Order $order)
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
    private function getOrderWeight(SnipcartOrder $snipcartOrder, Package $package): Weight
    {
        $orderWeight = $snipcartOrder->totalWeight;

        if (! empty($package->weight)) {
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
    private function getOrderNotes($customFields)
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
    private function getGiftNote($customFields)
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
    private function getShipStationRatesForOrder(
        SnipcartOrder $snipcartOrder,
        Package $package
    ): array {
        $dimensions = Dimensions::populateFromSnipcartPackage($package);
        $weight = $this->getOrderWeight($snipcartOrder, $package);
        $shipmentInfo = $this->prepShipmentInfo(
            $snipcartOrder,
            $dimensions,
            $weight
        );

        $responseData = $this->post(
            'shipments/getrates',
            $shipmentInfo
        );

        if ($responseData === null) {
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
    private function getShippingMethodFromOrder(SnipcartOrder $order)
    {
        /**
         * First try and find a matching rate quote, which would have preceded
         * the completed order.
         */
        $rateQuote = Snipcart::$plugin->shipments->getQuoteLogForOrder($order);

        if (! empty($rateQuote)) {
            $rate = $this->getMatchingRateFromLog($rateQuote, $order);

            if ($rate !== null) {
                return $rate;
            }
        }

        /**
         * If there wasn't a matching option, query the API for rates again
         * and look for the closest match.
         */
        return $this->getClosestRateForOrder($order);
    }

    /**
     * Check logged rates and return whichever previously-shown rate exactly
     * matches the rate cost and description for this order.
     *
     * @param $rateQuoteLog
     * @return Rate|null
     */
    private function getMatchingRateFromLog($rateQuoteLog, $order)
    {
        // get the rates that were already returned to Snipcart earlier
        $quoteRecord = Json::decode($rateQuoteLog->body, false);

        foreach ($quoteRecord->rates as $quotedRate) {
            /**
             * See if the collected shipping fees and service name are
             * an exact match.
             */
            $labelAndCostMatch = $quotedRate->description === $order->shippingMethod
                && (float)$quotedRate->cost === $order->shippingFees;

            if ($labelAndCostMatch) {
                return new Rate([
                    'serviceName'  => $quotedRate->description,
                    'serviceCode'  => $quotedRate->code ?? null,
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
    private function getClosestRateForOrder($order)
    {
        $closest = null;
        $package = Snipcart::$plugin->orders->getOrderPackaging($order);
        $rates   = $this->getShipStationRatesForOrder($order, $package);

        // check rates for matching name and/or price, otherwise take closest
        foreach ($rates as $rate) {
            /**
             * See if the collected shipping fees and service name are
             * an exact match.
             */
            $labelAndCostMatch = $rate->serviceName === $order->shippingMethod
                && ($rate->shipmentCost + $rate->otherCost) === $order->shippingFees;

            if ($labelAndCostMatch) {
                // return exact match
                return $rate;
            }

            if ($closest === null) {
                $closest = $rate;
                continue;
            }

            $currentRateDelta = abs($rate->shipmentCost - $order->shippingFees);
            $closestRateDelta = abs($closest->shipmentCost - $order->shippingFees);

            if ($currentRateDelta < $closestRateDelta) {
                // use the rate that has the least cost difference
                $closest = $rate;
            }
        }

        return $closest;
    }

}
