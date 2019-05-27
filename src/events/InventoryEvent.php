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
     * @var Element The Craft Element containing Snipcart product details.
     */
    public $element;

    /**
     * @var int The value (+/-) that should be added to the existing inventory.
     */
    public $quantity;

}
