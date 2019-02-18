<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\services;

use workingconcept\snipcart\events\InventoryEvent;
use workingconcept\snipcart\helpers\FieldHelper;
use craft\elements\Entry;
use Craft;

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
     * Trigger an Event that will allow another plugin or module to adjust
     * product inventory for a relevant Entry.
     *
     * @param Entry $entry     Entry that's used as a product definition
     * @param int   $quantity  a whole number representing the quantity change
     * @throws
     */
    public function reduceProductInventory($entry, $quantity)
    {
        // subtract the order quantity
        $quantityToAdjust = - $quantity;
        $fieldHandle = FieldHelper::getProductInfoFieldHandle($entry);
        $usesInventory = isset($fieldHandle) &&
            $entry->{$fieldHandle}->inventory !== null;

        if (! $usesInventory)
        {
            return;
        }

        if ($this->hasEventHandlers(self::EVENT_PRODUCT_INVENTORY_CHANGE))
        {
            $event = new InventoryEvent([
                'entry'    => $entry,
                'quantity' => $quantityToAdjust,
            ]);

            $this->trigger(self::EVENT_PRODUCT_INVENTORY_CHANGE, $event);

            /**
             * Allow an event handler to override the quantity change before
             * it gets adjusted.
             */
            $quantityToAdjust = $event->quantity;
        }

        if ($fieldHandle)
        {
            $originalQuantity = $entry->{$fieldHandle}->inventory;
            $newQuantity      = $originalQuantity + $quantityToAdjust;

            if ($originalQuantity > 0 && $originalQuantity !== $newQuantity)
            {
                $entry->{$fieldHandle}->inventory = $originalQuantity + $quantityToAdjust;
                Craft::$app->getElements()->saveElement($entry);
            }
        }
    }


    // Private Methods
    // =========================================================================

}