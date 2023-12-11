<?php
namespace verbb\snipcart\events;

use craft\base\Element;

use yii\base\Event;

class InventoryEvent extends Event
{
    // Properties
    // =========================================================================

    public Element $element;
    public int $quantity;
}
