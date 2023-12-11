<?php
namespace verbb\snipcart\events;

use verbb\snipcart\models\snipcart\Order;
use yii\base\Event;

/**
 * Order status event class.
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */
class OrderStatusEvent extends Event
{
    /**
     * @var Order
     */
    public $order;

    /**
     * @var string
     */
    public $fromStatus;

    /**
     * @var string
     */
    public $toStatus;

}
