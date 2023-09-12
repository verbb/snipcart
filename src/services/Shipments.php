<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\services;

use Craft;
use craft\base\Component;
use fostercommerce\snipcart\errors\ShippingRateException;
use fostercommerce\snipcart\events\ShippingRateEvent;
use fostercommerce\snipcart\models\snipcart\Order;
use fostercommerce\snipcart\providers\shipstation\ShipStation;
use fostercommerce\snipcart\records\ShippingQuoteLog;
use fostercommerce\snipcart\Snipcart;
use yii\db\ActiveRecord;

/**
 * Class Shipments
 *
 * For interacting with external shipping providers.
 *
 * @package fostercommerce\snipcart\services
 * @property ShipStation $shipStation
 */
class Shipments extends Component
{
    /**
     * @event WebhookEvent Triggered before shipping rates are returned to Snipcart.
     */
    public const EVENT_BEFORE_RETURN_SHIPPING_RATES = 'beforeReturnShippingRates';

    /**
     * @var ShipStation Local reference to instantiated ShipStation provider
     */
    private $_shipStation;

    /**
     * Returns an instance of the ShipStation provider.
     */
    public function getShipStation(): ShipStation
    {
        if (! $this->_shipStation instanceof ShipStation) {
            $settings = Snipcart::$plugin->getSettings();
            return $this->_shipStation = $settings->getProvider('shipStation');
        }

        return $this->_shipStation;
    }

    /**
     * Collects shipping rate options for a Snipcart order.
     *
     * @return array [ 'rates' => ShippingRate[], 'package' => Package ] or [ 'errors' => [ ['key' => '...', 'message' => '...'] ] ]
     */
    public function collectRatesForOrder(Order $order): array
    {
        if ($order->hasShippableItems() === false) {
            Craft::warning(sprintf(
                'Snipcart order %s did not contain any shippable items.',
                $order->invoiceNumber ?? $order->token
            ), 'snipcart');

            return [];
        }

        try {
            $rates = [];
            $package = Snipcart::$plugin->orders->getOrderPackaging($order);

            $includeShipStationRates = $this->getShipStation()->isConfigured() &&
                $this->getShipStation()->getSettings()->enableShippingRates;

            if ($includeShipStationRates &&
                $shipStationRates = $this->getShipStation()->getRatesForOrder($order, $package)
            ) {
                $rates = array_merge($rates, $shipStationRates);
            }

            if ($this->hasEventHandlers(self::EVENT_BEFORE_RETURN_SHIPPING_RATES)) {
                $shippingRateEvent = new ShippingRateEvent([
                    'rates' => $rates,
                    'order' => $order,
                    'package' => $package,
                ]);

                $this->trigger(self::EVENT_BEFORE_RETURN_SHIPPING_RATES, $shippingRateEvent);

                if (! $shippingRateEvent->isValid) {
                    throw new ShippingRateException($shippingRateEvent);
                }

                $rates = $shippingRateEvent->rates;
            }
        } catch (ShippingRateException $shippingRateException) {
            Craft::warning(sprintf(
                'Snipcart plugin returned an error while fetching rates for %s',
                $order->invoiceNumber
            ), 'snipcart');

            return [
                'errors' => $shippingRateException->event->getErrors(),
            ];
        }

        if (empty($rates)) {
            Craft::warning(sprintf(
                'Snipcart plugin did not return any rates for %s',
                $order->invoiceNumber
            ), 'snipcart');
        }

        return [
            'rates' => $rates,
            'package' => $package,
        ];
    }

    /**
     * Handles an order that’s been completed, normally sent after
     * receiving a webhook post from Snipcart.
     *
     * @return object
     */
    public function handleCompletedOrder(Order $order)
    {
        $response = (object) [
            'orders' => [],
            'errors' => [],
        ];

        // is the plugin in test mode?
        $isTestMode = Snipcart::$plugin->getSettings()->testMode;

        // has ShipStation integration been configured?
        $shipStationConfigured = $this->getShipStation()->isConfigured();

        // does ShipStation's settings say we should send completed Snipcart orders?
        $shipStationShouldSend = $this->getShipStation()->getSettings()->sendCompletedOrders;

        // should we seriously do it?
        $sendToShipStation = ! $isTestMode &&
            $order->hasShippableItems() &&
            $shipStationConfigured &&
            $shipStationShouldSend;

        // send order to ShipStation if we need to
        if ($sendToShipStation) {
            $shipStationOrder = $this->getShipStation()->createOrder($order);
            $response->orders['shipStation'] = $shipStationOrder;

            if ((is_countable($shipStationOrder->getErrors()) ? count($shipStationOrder->getErrors()) : 0) > 0) {
                $response->errors['shipStation'] = $shipStationOrder->getErrors();
            }
        }

        return $response;
    }

    /**
     * Gets the last shipping rate quote that was returned for the given order.
     *
     * @param $order
     * @return array|ShippingQuoteLog|ActiveRecord|null
     */
    public function getQuoteLogForOrder($order)
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
