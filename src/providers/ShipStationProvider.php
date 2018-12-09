<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\providers;

use Craft;
use craft\base\Model;

class ShipStationProvider extends ShippingProvider
{
    protected $name = "ShipStation";

    public $apiKey;
    public $apiSecret;
}
