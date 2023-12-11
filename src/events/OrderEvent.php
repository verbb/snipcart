<?php
namespace verbb\snipcart\events;

use verbb\snipcart\models\snipcart\Order;

use yii\base\Event;

class OrderEvent extends Event
{
    // Properties
    // =========================================================================

    public Order $order;
}
