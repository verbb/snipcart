<?php
namespace verbb\snipcart\services;

use verbb\snipcart\Snipcart;
use verbb\snipcart\errors\ShippingRateException;
use verbb\snipcart\events\ShippingRateEvent;
use verbb\snipcart\models\snipcart\Order;
use verbb\snipcart\providers\shipstation\ShipStation;
use verbb\snipcart\records\ShippingQuoteLog;

use craft\base\Component;

use yii\db\ActiveRecord;

class Shipments extends Component
{
    // Constants
    // =========================================================================

    public const EVENT_BEFORE_RETURN_SHIPPING_RATES = 'beforeReturnShippingRates';


    // Properties
    // =========================================================================

    private ?ShipStation $_shipStation = null;


    // Public Methods
    // =========================================================================

    public function getShipStation(): ?ShipStation
    {
        if (!($this->_shipStation instanceof ShipStation)) {
            $settings = Snipcart::$plugin->getSettings();

            return $this->_shipStation = $settings->getProvider('shipStation');
        }

        return $this->_shipStation;
    }

    public function collectRatesForOrder(Order $order): array
    {
        if ($order->hasShippableItems() === false) {
            Snipcart::error('Snipcart order {num} did not contain any shippable items.', [
                'num' => $order->invoiceNumber ?? $order->token,
            ]);

            return [];
        }

        $shipStation = $this->getShipStation();

        if (!$shipstation) {
            return [];
        }

        try {
            $rates = [];
            $package = Snipcart::$plugin->getOrders()->getOrderPackaging($order);

            $includeShipStationRates = $shipStation->isConfigured() && $shipStation->getSettings()->enableShippingRates;

            if ($includeShipStationRates && $shipStationRates = $shipStation->getRatesForOrder($order, $package)) {
                $rates = array_merge($rates, $shipStationRates);
            }

            if ($this->hasEventHandlers(self::EVENT_BEFORE_RETURN_SHIPPING_RATES)) {
                $shippingRateEvent = new ShippingRateEvent([
                    'rates' => $rates,
                    'order' => $order,
                    'package' => $package,
                ]);

                $this->trigger(self::EVENT_BEFORE_RETURN_SHIPPING_RATES, $shippingRateEvent);

                if (!$shippingRateEvent->isValid) {
                    throw new ShippingRateException($shippingRateEvent);
                }

                $rates = $shippingRateEvent->rates;
            }
        } catch (ShippingRateException $shippingRateException) {
            Snipcart::error('Snipcart plugin returned an error while fetching rates for {num}', [
                'num' => $order->invoiceNumber,
            ]);

            return [
                'errors' => $shippingRateException->event->getErrors(),
            ];
        }

        if (empty($rates)) {
            Snipcart::error('Snipcart plugin did not return any rates for {num}', [
                'num' => $order->invoiceNumber,
            ]);
        }

        return [
            'rates' => $rates,
            'package' => $package,
        ];
    }

    public function handleCompletedOrder(Order $order): object
    {
        $response = (object) [
            'orders' => [],
            'errors' => [],
        ];

        $shipStation = $this->getShipStation();

        if (!$shipstation) {
            return $response;
        }

        // is the plugin in test mode?
        $isTestMode = Snipcart::$plugin->getSettings()->testMode;

        // has ShipStation integration been configured?
        $shipStationConfigured = $shipStation->isConfigured();

        // does ShipStation's settings say we should send completed Snipcart orders?
        $shipStationShouldSend = $shipStation->getSettings()->sendCompletedOrders;

        // should we seriously do it?
        $sendToShipStation = ! $isTestMode && $order->hasShippableItems() && $shipStationConfigured && $shipStationShouldSend;

        // send order to ShipStation if we need to
        if ($sendToShipStation) {
            $shipStationOrder = $shipStation->createOrder($order);
            $response->orders['shipStation'] = $shipStationOrder;

            if ((is_countable($shipStationOrder->getErrors()) ? count($shipStationOrder->getErrors()) : 0) > 0) {
                $response->errors['shipStation'] = $shipStationOrder->getErrors();
            }
        }

        return $response;
    }

    public function getQuoteLogForOrder($order): ShippingQuoteLog|array|ActiveRecord|null
    {
        return ShippingQuoteLog::find()
            ->where([
                'token' => $order->token,
            ])
            ->orderBy([
                'dateCreated' => SORT_DESC,
            ])
            ->one();
    }
}
