<?php
namespace verbb\snipcart\events;

use verbb\snipcart\models\snipcart\Order;
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
