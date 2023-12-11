<?php
namespace verbb\snipcart\providers\shipstation;

use verbb\snipcart\Snipcart;
use verbb\snipcart\base\ShippingProvider;
use verbb\snipcart\helpers\ModelHelper;
use verbb\snipcart\models\shipstation\Dimensions;
use verbb\snipcart\models\shipstation\Order;
use verbb\snipcart\models\shipstation\Rate;
use verbb\snipcart\models\shipstation\Weight;
use verbb\snipcart\models\snipcart\Order as SnipcartOrder;
use verbb\snipcart\models\snipcart\Package;
use verbb\snipcart\models\snipcart\ShippingRate as SnipcartRate;
use verbb\snipcart\providers\shipstation\events\OrderEvent;
use verbb\snipcart\providers\shipstation\Settings as ShipStationSettings;

use Craft;
use craft\helpers\App;
use craft\helpers\Json;

use GuzzleHttp\Client;

class ShipStation extends ShippingProvider
{
    // Constants
    // =========================================================================

    public const EVENT_BEFORE_SEND_ORDER = 'beforeSendOrder';
    public const TEST_ORDER_ID = 99_999_999;


    // Properties
    // =========================================================================

    protected Client $client;


    // Public Methods
    // =========================================================================

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
            return !empty($settings->apiKey) && ! empty($settings->apiSecret) && !empty($settings->defaultCountry) && !empty($settings->defaultOrderConfirmation) && !empty($settings->defaultWarehouseId);
        }

        return false;
    }

    public function getClient(): Client
    {
        if ($this->client instanceof Client) {
            return $this->client;
        }

        $key = $this->getSettings()->getPublicKey();
        $secret = $this->getSettings()->getSecretKey();

        return $this->client = Craft::createGuzzleClient([
            'base_uri' => self::apiBaseUrl(),
            'auth' => [$key, $secret],
            'headers' => [
                'Content-Type' => 'application/json; charset=utf-8',
                'Accept' => 'application/json',
            ],
            'verify' => false,
            'debug' => false,
        ]);
    }

    public function getRatesForOrder(SnipcartOrder $snipcartOrder, Package $package): array
    {
        $rates = [];

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

    public function createOrder(SnipcartOrder $snipcartOrder): mixed
    {
        $package = Snipcart::$plugin->getOrders()->getOrderPackaging($snipcartOrder);
        $order = Order::populateFromSnipcartOrder($snipcartOrder);

        $order->orderStatus = Order::STATUS_AWAITING_SHIPMENT;
        $order->customerNotes = $this->getOrderNotes($snipcartOrder->customFields);
        $order->giftMessage = $this->getGiftNote($snipcartOrder->customFields);
        $order->weight = $this->getOrderWeight($snipcartOrder, $package);
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

        if (($shippingMethod = $this->getShippingMethodFromOrder($snipcartOrder)) && !empty($shippingMethod->serviceCode)) {
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
                $order->orderId = self::TEST_ORDER_ID;
                return $order;
            }

            if (($createdOrder = $this->sendOrder($order)) instanceof Order) {
                // TODO: delete related rate quotes when order makes it to
                // ShipStation, or after a sensible amount of time
                 
                return $createdOrder;
            }

            Snipcart::error('Failed to create ShipStation order for {num}.', [
                'num' => $order->orderNumber,
            ]);

            return $order;
        }

        return $order;
    }

    public function createLabelForOrder(Order $order, bool $isTest = false, string $packageCode = 'package')
    {
        $payload = $order->toArray([], $order->extraFields(), true);

        $payload['testLabel'] = $isTest;
        $payload['packageCode'] = $packageCode;

        return $this->post('orders/createlabelfororder', $payload);
    }

    public function getOrderById($providerId): ?Order
    {
        $responseData = $this->get("order/$providerId");

        if (!empty($responseData)) {
            return new Order($responseData);
        }

        return null;
    }

    public function getOrderBySnipcartInvoice(string $snipcartInvoice): ?Order
    {
        $responseData = $this->get("orders?orderNumber=$snipcartInvoice");

        if ((is_countable($responseData->orders) ? count($responseData->orders) : 0) === 1) {
            return new Order($responseData->orders[0]);
        }

        return null;
    }

    protected function createSettingsModel(): ShipStationSettings
    {
        return new ShipStationSettings();
    }

    private function prepShipmentInfo(SnipcartOrder $snipcartOrder, Dimensions $dimensions, Weight $weight): array
    {
        $model = Snipcart::$plugin->getSettings();

        $shipmentInfo = [
            'carrierCode' => $this->getSettings()->defaultCarrierCode,
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

    private function sendOrder(Order $order): Order
    {
        $responseData = $this->post('orders/createorder', $order->getPayloadForPost());

        return new Order($responseData);
    }

    private function getOrderWeight(SnipcartOrder $snipcartOrder, Package $package): Weight
    {
        $orderWeight = $snipcartOrder->totalWeight;

        if (!empty($package->weight)) {
            $orderWeight += $package->weight; // add packing material weight
        }

        $weight = new Weight([
            'value' => $orderWeight,
            'units' => Weight::UNIT_GRAMS,
        ]);

        $weight->validate();

        return $weight;
    }

    private function getOrderNotes(?array $customFields): ?string
    {
        return $this->getValueFromCustomFields($customFields, Snipcart::$plugin->getSettings()->orderCommentsFieldName, true);
    }

    private function getGiftNote(?array $customFields): ?string
    {
        return $this->getValueFromCustomFields($customFields, Snipcart::$plugin->getSettings()->orderGiftNoteFieldName, true);
    }

    private function getShipStationRatesForOrder(SnipcartOrder $snipcartOrder, Package $package): array
    {
        $dimensions = Dimensions::populateFromSnipcartPackage($package);
        $weight = $this->getOrderWeight($snipcartOrder, $package);
        $shipmentInfo = $this->prepShipmentInfo($snipcartOrder, $dimensions, $weight);

        $responseData = $this->post('shipments/getrates', $shipmentInfo);

        if ($responseData === null) {
            Snipcart::error('ShipStation did not return any rates for {num}', [
                'num' => $snipcartOrder->invoiceNumber,
            ]);

            return [];
        }

        return ModelHelper::populateArrayWithModels($responseData, Rate::class);
    }

    private function getShippingMethodFromOrder(SnipcartOrder $snipcartOrder): ?Rate
    {
        $rateQuote = Snipcart::$plugin->getShipments()->getQuoteLogForOrder($snipcartOrder);

        if (!empty($rateQuote)) {
            $rate = $this->getMatchingRateFromLog($rateQuote, $snipcartOrder);

            if ($rate instanceof Rate) {
                return $rate;
            }
        }

        return $this->getClosestRateForOrder($snipcartOrder);
    }

    private function getMatchingRateFromLog($rateQuoteLog, SnipcartOrder $snipcartOrder): ?Rate
    {
        $quoteRecord = Json::decode($rateQuoteLog->body, false);

        foreach ($quoteRecord->rates as $quotedRate) {
            $labelAndCostMatch = $quotedRate->description === $snipcartOrder->shippingMethod && (float) $quotedRate->cost === $snipcartOrder->shippingFees;

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

    private function getClosestRateForOrder(SnipcartOrder $snipcartOrder): ?Rate
    {
        $closest = null;
        $package = Snipcart::$plugin->getOrders()->getOrderPackaging($snipcartOrder);
        $rates = $this->getShipStationRatesForOrder($snipcartOrder, $package);

        foreach ($rates as $rate) {
            $labelAndCostMatch = $rate->serviceName === $snipcartOrder->shippingMethod && ($rate->shipmentCost + $rate->otherCost) === $snipcartOrder->shippingFees;

            if ($labelAndCostMatch) {
                return $rate;
            }

            if (!$closest instanceof Rate) {
                $closest = $rate;
                continue;
            }

            $currentRateDelta = abs($rate->shipmentCost - $snipcartOrder->shippingFees);
            $closestRateDelta = abs($closest->shipmentCost - $snipcartOrder->shippingFees);

            if ($currentRateDelta < $closestRateDelta) {
                $closest = $rate;
            }
        }

        return $closest;
    }
}
