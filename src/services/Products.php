<?php
namespace verbb\snipcart\services;

use verbb\snipcart\events\InventoryEvent;
use verbb\snipcart\helpers\FieldHelper;
use verbb\snipcart\models\snipcart\Item;
use verbb\snipcart\Snipcart;

use Craft;
use craft\base\Component;
use Throwable;

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
        $itemId = $orderItem->id;

        Snipcart::info('Attempting to reduce inventory for order item.', [
            'itemId' => $itemId,
            'quantity' => $orderItem->quantity,
            'quantityToAdjust' => $quantityToAdjust,
        ]);

        // get the Entry or Matrix block owning the Product Details field
        try {
            $element = $orderItem->getRelatedElement();
        } catch (Throwable $e) {
            Snipcart::error('Inventory update failed while resolving related element for `{sku}`: {message}', [
                'sku' => $itemId,
                'message' => $e->getMessage(),
            ]);

            return;
        }

        if (!$element) {
            Snipcart::info('Inventory update skipped: no related element found.', [
                'itemId' => $itemId,
            ]);

            return;
        }

        // get the Product Details field handle
        $fieldHandle = FieldHelper::getProductInfoFieldHandle($element);

        if (!$fieldHandle) {
            Snipcart::info('Inventory update skipped: Product Details field not found on element.', [
                'itemId' => $itemId,
                'elementId' => $element->id ?? null,
                'fieldHandle' => $fieldHandle,
            ]);

            return;
        }

        try {
            $fieldValue = $element->getFieldValue($fieldHandle);
        } catch (Throwable $e) {
            Snipcart::error('Inventory update failed while reading field value for `{sku}`: {message}', [
                'sku' => $itemId,
                'message' => $e->getMessage(),
                'fieldHandle' => $fieldHandle,
                'elementId' => $element->id ?? null,
            ]);

            return;
        }

        if (!$fieldValue || !isset($fieldValue->inventory) || $fieldValue->inventory === null) {
            Snipcart::info('Inventory update skipped: inventory value missing.', [
                'itemId' => $itemId,
                'elementId' => $element->id ?? null,
                'fieldHandle' => $fieldHandle,
            ]);

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
            $originalQuantity = $fieldValue->inventory;
            $newQuantity = $originalQuantity + $quantityToAdjust;

            Snipcart::info('Calculated inventory update values.', [
                'itemId' => $itemId,
                'elementId' => $element->id ?? null,
                'fieldHandle' => $fieldHandle,
                'originalQuantity' => $originalQuantity,
                'quantityToAdjust' => $quantityToAdjust,
                'newQuantity' => $newQuantity,
            ]);

            if ($originalQuantity > 0 && $originalQuantity !== $newQuantity) {
                $fieldValue->inventory = $newQuantity;
                $element->setFieldValue($fieldHandle, $fieldValue);

                $saveResult = Craft::$app->getElements()->saveElement($element);

                Snipcart::info('Attempted to save updated inventory.', [
                    'itemId' => $itemId,
                    'elementId' => $element->id ?? null,
                    'fieldHandle' => $fieldHandle,
                    'saveResult' => $saveResult,
                    'errors' => $element->getErrors(),
                ]);
            } else {
                Snipcart::info('Inventory update skipped: quantity unchanged or original quantity not positive.', [
                    'itemId' => $itemId,
                    'elementId' => $element->id ?? null,
                    'originalQuantity' => $originalQuantity,
                    'newQuantity' => $newQuantity,
                ]);
            }
        }
    }
}
