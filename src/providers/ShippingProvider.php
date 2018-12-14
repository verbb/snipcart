<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\providers;

use craft\base\Model;

class ShippingProvider extends Model
{
    public $name;

    /**
     * Get shipping rates for the provided Snipcart order.
     */
    /*
    public function fetchRatesForOrder($order)
    {
    }

    public function createShipment($order)
    {
    }

    public function createShippingLabel($order)
    {
    }
    *

    /**
     * Translate the provided Snipcart order into shipping provider model.
     */
    /*
    public function translateOrder()
    {
    }
    */
}
