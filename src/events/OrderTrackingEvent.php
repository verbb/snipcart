<?php
namespace verbb\snipcart\events;

use verbb\snipcart\models\snipcart\Order;

use yii\base\Event;

class OrderTrackingEvent extends Event
{
    // Properties
    // =========================================================================

    public Order $order;
    public string $trackingNumber;
    public string $trackingUrl;
}
