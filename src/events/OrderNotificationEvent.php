<?php
namespace verbb\snipcart\events;

use verbb\snipcart\models\snipcart\Notification;

use yii\base\Event;

class OrderNotificationEvent extends Event
{
    // Properties
    // =========================================================================

    public Notification $notification;
}
