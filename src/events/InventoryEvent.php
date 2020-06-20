<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\events;

use craft\base\Element;
use yii\base\Event;
use craft\elements\Entry;

/**
 * Inventory event class.
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */
class InventoryEvent extends Event
{
    /**
     * @var Element The Craft Element containing Snipcart product details.
     */
    public $element;

    /**
     * @var Entry The Craft Entry containing Snipcart product details.
     * @deprecated in 1.1. Use $element instead.
     */
    public $entry;

    /**
     * @var int The value (+/-) that should be added to the existing inventory.
     */
    public $quantity;

}
