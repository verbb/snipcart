<?php
namespace verbb\snipcart\events;

use verbb\snipcart\models\snipcart\Order;

use yii\base\Event;

class OrderStatusEvent extends Event
{
    // Properties
    // =========================================================================

    public Order $order;
    public string $fromStatus;
    public string $toStatus;
}
