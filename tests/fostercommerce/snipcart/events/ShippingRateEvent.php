<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\events;

use fostercommerce\snipcart\models\snipcart\Order;
use fostercommerce\snipcart\models\snipcart\ShippingRate;
use fostercommerce\snipcart\models\snipcart\Package;
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
