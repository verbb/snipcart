<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\events;

use craft\elements\Entry;
use yii\base\Event;

/**
 * Inventory event class.
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */
class InventoryEvent extends Event
{
    // Properties
    // =========================================================================

    /**
     * @var Entry The Snipcart product's Craft Entry.
     */
    public $entry;

    /**
     * @var int The value (+/-) that should be added to the existing inventory.
     */
    public $quantity;

}
