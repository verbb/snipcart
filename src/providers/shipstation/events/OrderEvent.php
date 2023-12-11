<?php
namespace verbb\snipcart\providers\shipstation\events;

use verbb\snipcart\models\shipstation\Order;

use yii\base\Event;

class OrderEvent extends Event
{
    // Properties
    // =========================================================================

    public Order $order;
}
