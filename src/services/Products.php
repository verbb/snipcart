<?php
namespace verbb\snipcart\services;

use verbb\snipcart\events\InventoryEvent;
use verbb\snipcart\helpers\FieldHelper;
use verbb\snipcart\models\snipcart\Item;

use Craft;
use craft\base\Component;

class Products extends Component
{
    // Constants
    // =========================================================================

    public const EVENT_PRODUCT_INVENTORY_CHANGE = 'productInventoryChange';


    // Public Methods
    // =========================================================================

    public function reduceInventory(Item $orderItem): void
    {
        // subtract the order quantity
        $quantityToAdjust = -$orderItem->quantity;

        // get the Entry or Matrix block owning the Product Details field
        $element = $orderItem->getRelatedElement();

        // get the Product Details field handle
        $fieldHandle = FieldHelper::getProductInfoFieldHandle($element);

        // does that field handle exist and have a value?
        $usesInventory = isset($fieldHandle) && $element->{$fieldHandle}->inventory !== null;

        if (!$usesInventory) {
            return;
        }

        if ($this->hasEventHandlers(self::EVENT_PRODUCT_INVENTORY_CHANGE)) {
            $inventoryEvent = new InventoryEvent([
                'element' => $element,
                'quantity' => $quantityToAdjust,
            ]);

            $this->trigger(self::EVENT_PRODUCT_INVENTORY_CHANGE, $inventoryEvent);

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
}
