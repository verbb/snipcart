<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\services;

use workingconcept\snipcart\models\Order;
use workingconcept\snipcart\Snipcart;
use workingconcept\snipcart\events\WebhookEvent;
use workingconcept\snipcart\models\Settings;
use workingconcept\snipcart\providers\ShipStation;
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
    // Constants
    // =========================================================================

    /**
     * @event WebhookEvent Triggered before shipping rates are returned to Snipcart.
     */
    const EVENT_BEFORE_RETURN_SHIPPING_RATES = 'beforeReturnShippingRates';

    
    // Private Properties
    // =========================================================================

    private $_shipStation;


    // Public Methods
    // =========================================================================

    /**
     * Returns an instance of the ShipStation provider.
     *
     * @return ShipStation
     */
    public function getShipStation(): ShipStation
    {
        if ($this->_shipStation === null)
        {
            return new ShipStation();
        }

        return $this->shipStation;
    }

    /**
     * Collect shipping rate options for a Snipcart order.
     *
     * @param Order $order
     * @return array [ 'rates' => ShippingRate[], 'package' => Package ]
     */
    public function collectRatesForOrder(Order $order): array
    {
        if ($order->hasShippableItems() === false)
        {
            Craft::warning(sprintf(
                'Snipcart order %s did not contain any shippable items.',
                $order->invoiceNumber ?? $order->token
            ), 'snipcart');

            return [];
        }

        $rates = [];
        $package = Snipcart::$plugin->orders->getOrderPackaging($order);
        
        $includeShipStationRates = in_array(
            Settings::PROVIDER_SHIPSTATION,
            Snipcart::$plugin->getSettings()->enabledProviders,
            true
        );

        if (
            $includeShipStationRates &&
            $shipStationRates = $this->getShipStation()->getRatesForOrder($order, $package)
        ) {
            $rates = array_merge($rates, $shipStationRates);
        }

        if ($this->hasEventHandlers(self::EVENT_BEFORE_RETURN_SHIPPING_RATES))
        {
            $event = new WebhookEvent([
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
     * Handle an order we've received via webhook.
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

        // is ShipStation an enabled provider?
        $sendToShipStation = in_array(
            Settings::PROVIDER_SHIPSTATION,
            Snipcart::$plugin->getSettings()->enabledProviders,
            false
        );

        // send order to ShipStation if we need to
        if ($sendToShipStation)
        {
            $shipStationOrder = $this->getShipStation()->createOrder($order);
            $response->orders['shipStation'] = $shipStationOrder;

            if (count($shipStationOrder->getErrors()) > 0)
            {
                $response->errors['shipStation'] = $shipStationOrder->getErrors();
            }
        }

        return $response;
    }

    /**
     * Get the last shipping rate quote that was returned for the given order.
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

    // Private Methods
    // =========================================================================

}