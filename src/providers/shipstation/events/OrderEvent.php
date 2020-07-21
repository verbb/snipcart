<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\providers\shipstation\events;

use workingconcept\snipcart\models\shipstation\Order;
use yii\base\Event;

/**
 * Order event class.
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */
class OrderEvent extends Event
{
    /**
     * @var Order
     */
    public $order;

}
