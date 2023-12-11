<?php
namespace verbb\snipcart\providers\shipstation\events;

use verbb\snipcart\models\shipstation\Order;
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
