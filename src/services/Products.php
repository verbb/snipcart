<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\services;

use workingconcept\snipcart\Snipcart;
use workingconcept\snipcart\events\InventoryEvent;
use craft\elements\Entry;

/**
 * The Products service lets you interact with Snipcart products as tidy,
 * documented models. The service can be accessed globally from
 * `Snipcart::$plugin->products`.
 *
 * Products become Items once added to a cart and/or purchased.
 *
 * @package workingconcept\snipcart\services
 */
class Products extends \craft\base\Component
{
    // Constants
    // =========================================================================

    /**
     * @event InventoryEvent Triggered when a product's inventory has changed
     *                       because an order was created or updated.
     */
    const EVENT_PRODUCT_INVENTORY_CHANGE = 'productInventoryChange';


    // Public Methods
    // =========================================================================

    /**
     * Return a Craft Element that matches Snipcart's supplied product ID.
     *
     * @param  string $id  the unique ID Snipcart provided
     *
     * @return Entry|false matching Craft Element or false
     */
    public function getProductElementById($id)
    {
        // TODO: support any Element type, not just Entry

        $productIdentifier = Snipcart::$plugin->getSettings()->productIdentifier;

        if ($productIdentifier === 'id')
        {
            $element = Entry::find()
                ->id($id)
                ->one();
        }
        else
        {
            $element = Entry::find()
                ->where($productIdentifier, $id)
                ->one();
        }

        if ( ! empty($element))
        {
            if (is_array($element))
            {
                return $element[0];
            }

            return $element;
        }

        return false;
    }

    /**
     * Trigger an Event that will allow another plugin or module to adjust
     * product inventory for a relevant Entry.
     *
     * @param Entry $entry     Entry that's used as a product definition
     * @param int   $quantity  a whole number representing the quantity change
     *                         (normally negative)
     */
    public function reduceProductInventory($entry, $quantity)
    {
        // TODO: go ahead and decrement quantity if that setting is enabled

        if ($this->hasEventHandlers(self::EVENT_PRODUCT_INVENTORY_CHANGE))
        {
            $event = new InventoryEvent([
                'entry'    => $entry,
                'quantity' => - $quantity,
            ]);

            $this->trigger(self::EVENT_PRODUCT_INVENTORY_CHANGE, $event);
        }
    }


    // Private Methods
    // =========================================================================

}