<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\events;

use workingconcept\snipcart\models\Order;
use workingconcept\snipcart\models\ShippingRate;
use workingconcept\snipcart\models\Package;
use yii\base\Event;

/**
 * Webhook event class.
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */
class WebhookEvent extends Event
{
    // Properties
    // =========================================================================

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
