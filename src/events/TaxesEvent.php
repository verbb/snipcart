<?php
namespace verbb\snipcart\events;

use verbb\snipcart\models\snipcart\Order;

use yii\base\Event;

class TaxesEvent extends Event
{
    // Properties
    // =========================================================================

    public Order $order;
    public array $taxes = [];
}
