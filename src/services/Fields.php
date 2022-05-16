<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\services;

use craft\elements\Entry;
use fostercommerce\snipcart\fields\ProductDetails;
use fostercommerce\snipcart\models\ProductDetails as ProductDetailsModel;
use fostercommerce\snipcart\records\ProductDetails as ProductDetailsRecord;
use Craft;
use craft\base\ElementInterface;
use fostercommerce\snipcart\Snipcart;

/**
 * @package fostercommerce\snipcart\services
 */
class Fields extends \craft\base\Component
{
    /**
     * Saves data for a Product Details field.
     *
     * @param ProductDetails   $field   Related Field instance
     * @param ElementInterface $element Related Element
     *
     * @return bool|null
     * @throws
     */
    public function saveProductDetailsField($field, $element)
    {
        $data = $element->getFieldValue($field->handle);

        if (empty($data)) {
            return null;
        }

        $currentSiteId = Craft::$app->getSites()->getCurrentSite()->id;

        return $this->saveRecord(
            $data,
            $element->siteId ?? $currentSiteId,
            $element->getId(),
            $field->id
        );
    }

    /**
     * Gets data for a Product Details field.
     *
     * @param ProductDetails        $field   Related Field
     * @param ElementInterface|null $element Related Element
     * @param mixed                 $value   Data that should be used
     *                                       to populate the model
     *
     * @return ProductDetailsModel|null
     * @throws
     */
    public function getProductDetailsField($field, ElementInterface $element = null, $value = null)
    {   
        sleep(3); // PETE: This sucks but makes things work
        // if we’ve already got a model, just give it back
        if ($value instanceof ProductDetailsModel) {
            return $value;
        }

        // if we don’t have an element, we don't have much to do
        if (! $element instanceof ElementInterface) {
            return null;
        }

        $siteId = $element->siteId;
        $elementId = $element->id;
        
        if (is_array($value)) {
            $model = new ProductDetailsModel($value);

            $model->fieldId = $field->id;
            $model->siteId  = $siteId;

            if ($elementId !== null) {
                $model->elementId = $elementId;
            }

            return $model;
        }

        // if we have an Entry, we’re working with a source ID and need the corresponding Element ID
        if (is_a($element, Entry::class) &&
            $currentRevision = $element->getCurrentRevision()
        ) {
            $elementId = $currentRevision->getId();
        }

        /**
         * Populate a ProductDetailsModel on an existing Element.
         */
        if ($elementId !== null &&
            $record = $this->getRecord(
                $siteId,
                $elementId,
                $field->id
            )
        ) {

            if (! $this->isUnsavedRecord($record)) {
                // populate with stored values
                return new ProductDetailsModel($record->getAttributes());
            }

            $model = new ProductDetailsModel();

            /**
             * Populate empty model with defaults, being sure fieldId is
             * set since defaults depend on field configuration.
             */
            $model->fieldId = $field->id;
            $model->populateDefaults();

            return $model;
        }

        $model = new ProductDetailsModel();

        $model->fieldId = $field->id;
        $model->siteId  = $siteId;

        if ($elementId !== null) {
            $model->elementId = $elementId;
        }

        $model->populateDefaults();

        return $model;
    }

    /**
     * Returns true if the record has not yet been saved to the database, or
     * if it was created without yet being populated like during a bulk Element
     * re-save after the field is newly added.
     *
     * @param ProductDetailsRecord $record
     * @return bool
     */
    private function isUnsavedRecord($record): bool
    {
        if ($record->isNew) {
            return true;
        }

        /**
         * A record can only have a `null` sku and price if saved during a
         * bulk operation.
         */
        return $record->sku === null && $record->price === null;
    }

    /**
     * Saves the record that stores the field data.
     *
     * @param \stdClass  $data       Field data to be saved
     * @param int        $siteId     Relevant Site ID
     * @param int        $elementId  Relevant Element ID
     * @param int        $fieldId    Relevant Field ID
     *
     * @return bool
     */
    private function saveRecord($data, $siteId, $elementId, $fieldId): bool
    {
        $record = $this->getRecord($siteId, $elementId, $fieldId);

        $record->setAttributes([
            'sku'            => $data->sku,
            'price'          => $data->price,
            'shippable'      => $data->shippable,
            'taxable'        => $data->taxable,
            'weight'         => $data->weight,
            'weightUnit'     => $data->weightUnit,
            'length'         => $data->length,
            'width'          => $data->width,
            'height'         => $data->height,
            'inventory'      => $data->inventory,
            'dimensionsUnit' => $data->dimensionsUnit,
            'customOptions'  => $data->customOptions,
        ], false);

        return $record->save();
    }

    /**
     * Gets a ProductDetailsRecord with stored field data, or initializes
     * a new one.
     *
     * @param int  $siteId     Relevant Site ID
     * @param int  $elementId  Relevant Element ID
     * @param int  $fieldId    Relevant Field ID
     *
     * @return ProductDetailsRecord
     */
    private function getRecord($siteId, $elementId, $fieldId): ProductDetailsRecord
    {
        $record = ProductDetailsRecord::findOne([
            'siteId'    => $siteId,
            'elementId' => $elementId,
            'fieldId'   => $fieldId
        ]);

        if ($record === null) {
            $record = new ProductDetailsRecord();

            $record->isNew     = true;
            $record->siteId    = $siteId;
            $record->elementId = $elementId;
            $record->fieldId   = $fieldId;
        }

        return $record;
    }
}
