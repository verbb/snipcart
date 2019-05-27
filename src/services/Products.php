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
use workingconcept\snipcart\models\Item;
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
     * Adjusts the supplied Entry's product inventory by the quantity value if...
     *   a) it uses the Product Details field and
     *   b) its inventory value exists and is greater than zero
     *
     * `EVENT_PRODUCT_INVENTORY_CHANGE` will also be fired before the adjustment
     * so an Event hook can modifies the quantity property
     * prior to the adjustment.
     *
     * @param Item $orderItem   Snipcart Item that was part of a completed order
     *
     * @throws
     */
    public function reduceInventory($orderItem)
    {
        // subtract the order quantity
        $quantityToAdjust = - $orderItem->quantity;

        // get the Entry or Matrix block owning the Product Details field
        $element = $orderItem->getRelatedElement();

        // get the Product Details field handle
        $fieldHandle = FieldHelper::getProductInfoFieldHandle($element);

        // does that field handle exist and have a value?
        $usesInventory = isset($fieldHandle) &&
            $element->{$fieldHandle}->inventory !== null;

        if (! $usesInventory)
        {
            return;
        }

        if ($this->hasEventHandlers(self::EVENT_PRODUCT_INVENTORY_CHANGE))
        {
            $event = new InventoryEvent([
                'element'  => $element,
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
            $originalQuantity = $element->{$fieldHandle}->inventory;
            $newQuantity      = $originalQuantity + $quantityToAdjust;

            if ($originalQuantity > 0 && $originalQuantity !== $newQuantity)
            {
                $element->{$fieldHandle}->inventory = $originalQuantity + $quantityToAdjust;
                Craft::$app->getElements()->saveElement($element);
            }
        }
    }

}