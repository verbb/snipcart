<?php
namespace verbb\snipcart\services;

use verbb\snipcart\fields\ProductDetails;
use verbb\snipcart\models\ProductDetails as ProductDetailsModel;
use verbb\snipcart\records\ProductDetails as ProductDetailsRecord;

use Craft;
use craft\base\Component;
use craft\base\ElementInterface;
use craft\elements\Entry;

use stdClass;

class Fields extends Component
{
    // Public Methods
    // =========================================================================

    public function saveProductDetailsField(ProductDetails $field, ElementInterface $element): ?bool
    {
        $data = $element->getFieldValue($field->handle);

        if (empty($data)) {
            return null;
        }

        $currentSiteId = Craft::$app->getSites()->getCurrentSite()->id;

        return $this->saveRecord($data, $element->siteId ?? $currentSiteId, $element->getId(), $field->id);
    }

    public function getProductDetailsField(ProductDetails $field, ElementInterface $element = null, mixed $value = null): ?ProductDetailsModel
    {
        // if we’ve already got a model, just give it back
        if ($value instanceof ProductDetailsModel) {
            return $value;
        }

        // if we don’t have an element, we don't have much to do
        if (!$element instanceof ElementInterface) {
            return null;
        }

        $siteId = $element->siteId;
        $elementId = $element->id;

        if (is_array($value)) {
            $model = new ProductDetailsModel($value);

            $model->fieldId = $field->id;
            $model->siteId = $siteId;

            if ($elementId !== null) {
                $model->elementId = $elementId;
            }

            return $model;
        }

        // if we have an Entry, we’re working with a source ID and need the corresponding Element ID
        if ($element instanceof Entry &&
            $currentRevision = $element->getCurrentRevision()
        ) {
            $elementId = $currentRevision->getId();
        }

        // Populate a ProductDetailsModel on an existing Element.
        if ($elementId !== null && $record = $this->getRecord($siteId, $elementId, $field->id)) {
            if (!$this->isUnsavedRecord($record)) {
                // populate with stored values
                return new ProductDetailsModel($record->getAttributes());
            }

            $model = new ProductDetailsModel();

            // Populate empty model with defaults, being sure fieldId is
            // set since defaults depend on field configuration.
            $model->fieldId = $field->id;
            $model->populateDefaults();

            return $model;
        }

        $model = new ProductDetailsModel();

        $model->fieldId = $field->id;
        $model->siteId = $siteId;

        if ($elementId !== null) {
            $model->elementId = $elementId;
        }

        $model->populateDefaults();

        return $model;
    }


    // Private Methods
    // =========================================================================

    private function isUnsavedRecord(ProductDetailsRecord $productDetailsRecord): bool
    {
        if ($productDetailsRecord->isNew) {
            return true;
        }

        // A record can only have a `null` sku and price if saved during a bulk operation.
        return $productDetailsRecord->sku === null && $productDetailsRecord->price === null;
    }

    private function saveRecord(stdClass $data, int $siteId, int $elementId, int $fieldId): bool
    {
        $productDetailsRecord = $this->getRecord($siteId, $elementId, $fieldId);

        $productDetailsRecord->setAttributes([
            'sku' => $data->sku,
            'price' => $data->price,
            'shippable' => $data->shippable,
            'taxable' => $data->taxable,
            'weight' => $data->weight,
            'weightUnit' => $data->weightUnit,
            'length' => $data->length,
            'width' => $data->width,
            'height' => $data->height,
            'inventory' => $data->inventory,
            'dimensionsUnit' => $data->dimensionsUnit,
            'customOptions' => $data->customOptions,
        ], false);

        return $productDetailsRecord->save();
    }

    private function getRecord(int $siteId, int $elementId, int $fieldId): ProductDetailsRecord
    {
        $record = ProductDetailsRecord::findOne([
            'siteId' => $siteId,
            'elementId' => $elementId,
            'fieldId' => $fieldId,
        ]);

        if (!$record instanceof ProductDetailsRecord) {
            $record = new ProductDetailsRecord();

            $record->isNew = true;
            $record->siteId = $siteId;
            $record->elementId = $elementId;
            $record->fieldId = $fieldId;
        }

        return $record;
    }
}
