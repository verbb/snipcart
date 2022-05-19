<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\providers\shipstation;

use GuzzleHttp\Client;
use fostercommerce\snipcart\errors\ShippingRateException;
use Craft;
use craft\helpers\Json;
use fostercommerce\snipcart\base\ShippingProvider;
use fostercommerce\snipcart\helpers\ModelHelper;
use fostercommerce\snipcart\helpers\VersionHelper;
use fostercommerce\snipcart\models\shipstation\Dimensions;
use fostercommerce\snipcart\models\shipstation\Order;
use fostercommerce\snipcart\models\shipstation\Rate;
use fostercommerce\snipcart\models\shipstation\Weight;
use fostercommerce\snipcart\models\snipcart\Order as SnipcartOrder;
use fostercommerce\snipcart\models\snipcart\Package;
use fostercommerce\snipcart\models\snipcart\ShippingRate as SnipcartRate;
use fostercommerce\snipcart\providers\shipstation\events\OrderEvent;
use fostercommerce\snipcart\providers\shipstation\Settings as ShipStationSettings;
use fostercommerce\snipcart\Snipcart;

/**
 * Class ShipStation
 *
 * @package fostercommerce\snipcart\providers
 * @todo log exceptions for troubleshooting
 */
class ShipStation extends ShippingProvider
{
    /**
     * @event WebhookEvent Triggered before an order is sent to ShipStation.
     */
    public const EVENT_BEFORE_SEND_ORDER = 'beforeSendOrder';

    /**
     * @var int  fake ID returned to simulate success in test mode
     */
    public const TEST_ORDER_ID = 99_999_999;

    /**
     * @var Client
     */
    protected $client;

    public static function refHandle(): string
    {
        return 'shipStation';
    }

    public static function apiBaseUrl(): string
    {
        return 'https://ssapi.shipstation.com/';
    }

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

    public function getClient(): Client
    {
        if ($this->client instanceof Client) {
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
            'auth' => [$key, $secret],
            'headers' => [
                'Content-Type' => 'application/json; charset=utf-8',
                'Accept' => 'application/json',
            ],
            'verify' => false,
            'debug' => false,
        ];

        $this->client = Craft::createGuzzleClient($clientConfig);

        return $this->client;
    }

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
                'cost' => number_format($rate->shipmentCost + $rate->otherCost, 2),
                'description' => $rate->serviceName,
                'code' => $rate->serviceCode,
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
        $order = Order::populateFromSnipcartOrder($snipcartOrder);

        $order->orderStatus = Order::STATUS_AWAITING_SHIPMENT;
        $order->customerNotes = $this->getOrderNotes($snipcartOrder->customFields);
        $order->giftMessage = $this->getGiftNote($snipcartOrder->customFields);
        $order->weight = $this->getOrderWeight($snipcartOrder, $package);

        // it's a gift order if it has a gift message
        $order->gift = $order->giftMessage !== null;

        if ($package->hasPhysicalDimensions()) {
            $order->dimensions = new Dimensions([
                'length' => $package->length,
                'width' => $package->width,
                'height' => $package->height,
                'units' => Dimensions::UNIT_INCHES,
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
                $orderEvent = new OrderEvent([
                    'order' => $order,
                ]);

                $this->trigger(self::EVENT_BEFORE_SEND_ORDER, $orderEvent);

                $order = $orderEvent->order;
            }

            if ($isDevMode || $isTestMode) {
                /**
                 * Don't transmit orders in devMode or testMode, just set a fake order ID.
                 */
                $order->orderId = self::TEST_ORDER_ID;
                return $order;
            }

            if (($createdOrder = $this->sendOrder($order)) instanceof Order) {
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
     * @param bool   $isTest       true if we only want to create a sample label
     * @param string $packageCode  package code to be sent
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

        $payload['testLabel'] = $isTest;
        $payload['packageCode'] = $packageCode;

        return $this->post('orders/createlabelfororder', $payload);
    }

    /**
     * @inheritdoc
     * https://www.shipstation.com/developer-api/#/reference/orders/getdelete-order/get-order
     */
    public function getOrderById($providerId): ?Order
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
    public function getOrderBySnipcartInvoice(string $snipcartInvoice): ?Order
    {
        $responseData = $this->get(sprintf(
            'orders?orderNumber=%s',
            $snipcartInvoice
        ));

        if ((is_countable($responseData->orders) ? count($responseData->orders) : 0) === 1) {
            return new Order($responseData->orders[0]);
        }

        return null;
    }

    protected function createSettingsModel(): ShipStationSettings
    {
        return new ShipStationSettings();
    }

    /**
     * Gets an array of shipment information for requesting a rate quote.
     */
    private function prepShipmentInfo(
        SnipcartOrder $snipcartOrder,
        Dimensions $dimensions,
        Weight $weight,
    ): array {
        $model = Snipcart::$plugin->getSettings();

        $shipmentInfo = [
            'carrierCode' => $this->getSettings()->defaultCarrierCode,
            //'serviceCode'  => '',
            'packageCode' => $this->getSettings()->defaultPackageCode,
            'fromPostalCode' => $model->shipFromAddress['postalCode'],
            'toCity' => $snipcartOrder->shippingAddress->city,
            'toState' => $snipcartOrder->shippingAddress->province,
            'toPostalCode' => $snipcartOrder->shippingAddress->postalCode,
            'toCountry' => $snipcartOrder->shippingAddress->country,
            'weight' => $weight->toArray(),
            'confirmation' => $this->getSettings()->defaultOrderConfirmation,
            'residential' => false,
        ];

        if ($dimensions->hasPhysicalDimensions()) {
            $shipmentInfo['dimensions'] = $dimensions->toArray();
        }

        return $shipmentInfo;
    }

    /**
     * Send the order to ShipStation via API.
     *
     * @return Order|null
     */
    private function sendOrder(Order $order): Order
    {
        $responseData = $this->post(
            'orders/createorder',
            $order->getPayloadForPost()
        );

        return new Order($responseData);
    }

    /**
     * Get a Weight model for the order, adding package weight when relevant.
     */
    private function getOrderWeight(SnipcartOrder $snipcartOrder, Package $package): Weight
    {
        $orderWeight = $snipcartOrder->totalWeight;

        if (! empty($package->weight)) {
            $orderWeight += $package->weight; // add packing material weight
        }

        $weight = new Weight([
            'value' => $orderWeight,
            'units' => Weight::UNIT_GRAMS,
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
     * @return Rate[]
     */
    private function getShipStationRatesForOrder(
        SnipcartOrder $snipcartOrder,
        Package $package,
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
            Craft::warning(sprintf(
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
     * @param SnipcartOrder $snipcartOrder Snipcart order.
     * @return Rate|null
     */
    private function getShippingMethodFromOrder(SnipcartOrder $snipcartOrder)
    {
        /**
         * First try and find a matching rate quote, which would have preceded
         * the completed order.
         */
        $rateQuote = Snipcart::$plugin->shipments->getQuoteLogForOrder($snipcartOrder);

        if (! empty($rateQuote)) {
            $rate = $this->getMatchingRateFromLog($rateQuote, $snipcartOrder);

            if ($rate instanceof Rate) {
                return $rate;
            }
        }

        /**
         * If there wasn't a matching option, query the API for rates again
         * and look for the closest match.
         */
        return $this->getClosestRateForOrder($snipcartOrder);
    }

    /**
     * Check logged rates and return whichever previously-shown rate exactly
     * matches the rate cost and description for this order.
     *
     * @param $rateQuoteLog
     */
    private function getMatchingRateFromLog($rateQuoteLog, SnipcartOrder $snipcartOrder): ?Rate
    {
        // get the rates that were already returned to Snipcart earlier
        $quoteRecord = Json::decode($rateQuoteLog->body, false);

        foreach ($quoteRecord->rates as $quotedRate) {
            /**
             * See if the collected shipping fees and service name are
             * an exact match.
             */
            $labelAndCostMatch = $quotedRate->description === $snipcartOrder->shippingMethod
                && (float) $quotedRate->cost === $snipcartOrder->shippingFees;

            if ($labelAndCostMatch) {
                return new Rate([
                    'serviceName' => $quotedRate->description,
                    'serviceCode' => $quotedRate->code ?? null,
                    'shipmentCost' => $quotedRate->cost,
                    'otherCost' => 0,
                ]);
            }
        }

        return null;
    }

    /**
     * Fetch new rates based on this order, and choose either the exact match
     * or next-closest rate to what was purchased and specified at checkout.
     *
     * @return Rate|null
     * @throws ShippingRateException
     */
    private function getClosestRateForOrder(SnipcartOrder $snipcartOrder)
    {
        $closest = null;
        $package = Snipcart::$plugin->orders->getOrderPackaging($snipcartOrder);
        $rates = $this->getShipStationRatesForOrder($snipcartOrder, $package);

        // check rates for matching name and/or price, otherwise take closest
        foreach ($rates as $rate) {
            /**
             * See if the collected shipping fees and service name are
             * an exact match.
             */
            $labelAndCostMatch = $rate->serviceName === $snipcartOrder->shippingMethod
                && ($rate->shipmentCost + $rate->otherCost) === $snipcartOrder->shippingFees;

            if ($labelAndCostMatch) {
                // return exact match
                return $rate;
            }

            if (! $closest instanceof Rate) {
                $closest = $rate;
                continue;
            }

            $currentRateDelta = abs($rate->shipmentCost - $snipcartOrder->shippingFees);
            $closestRateDelta = abs($closest->shipmentCost - $snipcartOrder->shippingFees);

            if ($currentRateDelta < $closestRateDelta) {
                // use the rate that has the least cost difference
                $closest = $rate;
            }
        }

        return $closest;
    }
}
