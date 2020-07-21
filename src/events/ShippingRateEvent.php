<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\events;

use workingconcept\snipcart\models\snipcart\Order;
use workingconcept\snipcart\models\snipcart\ShippingRate;
use workingconcept\snipcart\models\snipcart\Package;
use yii\base\Event;

/**
 * Shipping rate event class.
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */
class ShippingRateEvent extends Event
{
    /**
     * @var Order
     */
    public $order;

    /**
     * @var ShippingRate[]
     */
    public $rates;

    /**
     * @var Package
     */
    public $package;

}
