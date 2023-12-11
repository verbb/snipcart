<?php
namespace verbb\snipcart\events;

use verbb\snipcart\models\snipcart\Refund;

use yii\base\Event;

class OrderRefundEvent extends Event
{
    // Properties
    // =========================================================================

    public Refund $refund;
}
