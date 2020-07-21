<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\services;

use workingconcept\snipcart\events\ShippingRateEvent;
use workingconcept\snipcart\models\snipcart\Order;
use workingconcept\snipcart\Snipcart;
use workingconcept\snipcart\providers\shipstation\ShipStation;
use workingconcept\snipcart\records\ShippingQuoteLog;
use Craft;

/**
 * Class Shipments
 *
 * For interacting with external shipping providers.
 *
 * @package workingconcept\snipcart\services
 * @property ShipStation $shipStation
 */
class Shipments extends \craft\base\Component
{
    /**
     * @event WebhookEvent Triggered before shipping rates are returned to Snipcart.
     */
    const EVENT_BEFORE_RETURN_SHIPPING_RATES = 'beforeReturnShippingRates';

    /**
     * @var ShipStation Local reference to instantiated ShipStation provider
     */
    private $_shipStation;

    /**
     * Returns an instance of the ShipStation provider.
     *
     * @return ShipStation
     */
    public function getShipStation(): ShipStation
    {
        if ($this->_shipStation === null) {
            $settings = Snipcart::$plugin->getSettings();
            return $this->_shipStation = $settings->getProvider('shipStation');
        }

        return $this->_shipStation;
    }

    /**
     * Collects shipping rate options for a Snipcart order.
     *
     * @param Order $order
     *
     * @return array [ 'rates' => ShippingRate[], 'package' => Package ]
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
            $event = new ShippingRateEvent([
                'rates'   => $rates,
                'order'   => $order,
                'package' => $package
            ]);

            $this->trigger(self::EVENT_BEFORE_RETURN_SHIPPING_RATES, $event);

            $rates = $event->rates;
        }

        Craft::warning(sprintf(
            'Snipcart plugin did not return any rates for %s',
            $order->invoiceNumber
        ), 'snipcart');

        return [
            'rates'   => $rates,
            'package' => $package,
        ];
    }

    /**
     * Handles an order that’s been completed, normally sent after
     * receiving a webhook post from Snipcart.
     *
     * @param Order $order
     * @return object
     */
    public function handleCompletedOrder(Order $order)
    {
        $response = (object) [
            'orders' => [],
            'errors' => []
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

            if (count($shipStationOrder->getErrors()) > 0) {
                $response->errors['shipStation'] = $shipStationOrder->getErrors();
            }
        }

        return $response;
    }

    /**
     * Gets the last shipping rate quote that was returned for the given order.
     *
     * @param $order
     * @return array|ShippingQuoteLog|\yii\db\ActiveRecord|null
     */
    public function getQuoteLogForOrder($order)
    {
        return ShippingQuoteLog::find()
            ->where(['token' => $order->token])
            ->orderBy(['dateCreated' => SORT_DESC])
            ->one();
    }
}
