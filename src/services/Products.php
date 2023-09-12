<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\services;

use Craft;
use craft\base\Component;
use craft\elements\Entry;
use fostercommerce\snipcart\events\InventoryEvent;
use fostercommerce\snipcart\helpers\FieldHelper;
use fostercommerce\snipcart\models\snipcart\Item;

/**
 * The Products service lets you interact with Snipcart products as tidy,
 * documented models. The service can be accessed globally from
 * `Snipcart::$plugin->products`.
 *
 * Products become Items once added to a cart and/or purchased.
 *
 * @package fostercommerce\snipcart\services
 */
class Products extends Component
{
    /**
     * @event InventoryEvent Triggered when a product's inventory has changed
     *                       because an order was created or updated.
     */
    public const EVENT_PRODUCT_INVENTORY_CHANGE = 'productInventoryChange';

    /**
     * Adjusts the supplied Item's inventory value if...
     *   a) it uses the Product Details field and
     *   b) its inventory value is not null
     *
     * `EVENT_PRODUCT_INVENTORY_CHANGE` will also be fired before the adjustment
     * so an Event hook can modify the adjustment value prior to saving.
     *
     * @param Item $orderItem  Snipcart Item that was part of a completed order
     *
     * @throws
     */
    public function reduceInventory(Item $orderItem): void
    {
        // subtract the order quantity
        $quantityToAdjust = -$orderItem->quantity;

        // get the Entry or Matrix block owning the Product Details field
        $element = $orderItem->getRelatedElement();

        // get the Product Details field handle
        $fieldHandle = FieldHelper::getProductInfoFieldHandle($element);

        // does that field handle exist and have a value?
        $usesInventory = isset($fieldHandle) &&
            $element->{$fieldHandle}->inventory !== null;

        if (! $usesInventory) {
            return;
        }

        if ($this->hasEventHandlers(self::EVENT_PRODUCT_INVENTORY_CHANGE)) {
            $inventoryEvent = new InventoryEvent([
                'element' => $element,
                'entry' => $element,
                'quantity' => $quantityToAdjust,
            ]);

            $this->trigger(self::EVENT_PRODUCT_INVENTORY_CHANGE, $inventoryEvent);

            /**
             * Allow an event handler to override the quantity change before
             * it gets applied.
             */
            $quantityToAdjust = $inventoryEvent->quantity;
        }

        if ($fieldHandle !== '' && $fieldHandle !== '0') {
            $field = $element->{$fieldHandle};
            $originalQuantity = $field->inventory;
            $newQuantity = $originalQuantity + $quantityToAdjust;

            if ($originalQuantity > 0 && $originalQuantity !== $newQuantity) {
                $field->inventory = $newQuantity;
                $element->setFieldValue($fieldHandle, $field);
                Craft::$app->getElements()->saveElement($element);
            }
        }
    }

    /**
     * Adjusts the supplied Entry's product inventory by the quantity value if...
     *   a) it uses the Product Details field and
     *   b) its inventory value exists and is greater than zero
     *
     * `EVENT_PRODUCT_INVENTORY_CHANGE` will also be fired before the adjustment
     * so an Event hook can modify the quantity property prior to saving.
     *
     * @param Entry $entry     Entry that's used as a product definition
     * @param int   $quantity  Whole number representing the quantity change
     *
     * @throws
     *
     * @deprecated in 1.1. Use reduceInventory() instead.
     */
    public function reduceProductInventory($entry, $quantity): void
    {
        // subtract the order quantity
        $quantityToAdjust = -$quantity;
        $fieldHandle = FieldHelper::getProductInfoFieldHandle($entry);
        $usesInventory = isset($fieldHandle) &&
            $entry->{$fieldHandle}->inventory !== null;

        if (! $usesInventory) {
            return;
        }

        if ($this->hasEventHandlers(self::EVENT_PRODUCT_INVENTORY_CHANGE)) {
            $inventoryEvent = new InventoryEvent([
                'entry' => $entry,
                'quantity' => $quantityToAdjust,
            ]);

            $this->trigger(self::EVENT_PRODUCT_INVENTORY_CHANGE, $inventoryEvent);

            /**
             * Allow an event handler to override the quantity change before
             * it gets adjusted.
             */
            $quantityToAdjust = $inventoryEvent->quantity;
        }

        if ($fieldHandle !== '' && $fieldHandle !== '0') {
            $originalQuantity = $entry->{$fieldHandle}->inventory;
            $newQuantity = $originalQuantity + $quantityToAdjust;

            if ($originalQuantity > 0 && $originalQuantity !== $newQuantity) {
                $entry->{$fieldHandle}->inventory = $newQuantity;
                Craft::$app->getElements()->saveElement($entry);
            }
        }
    }
}
