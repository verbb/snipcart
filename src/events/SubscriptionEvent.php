<?php
namespace verbb\snipcart\events;

use verbb\snipcart\models\snipcart\Subscription;

use yii\base\Event;

class SubscriptionEvent extends Event
{
    // Properties
    // =========================================================================

    public Subscription $subscription;
}
