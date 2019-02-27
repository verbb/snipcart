<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\services;

use workingconcept\snipcart\fields\ProductDetails;
use workingconcept\snipcart\models\ProductDetails as ProductDetailsModel;
use workingconcept\snipcart\records\ProductDetails as ProductDetailsRecord;
use Craft;
use craft\base\ElementInterface;

/**
 * @package workingconcept\snipcart\services
 */
class Fields extends \craft\base\Component
{
    // Public Methods
    // =========================================================================

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

        if (empty($data))
        {
            return null;
        }

        return $this->_saveRecord(
            $data,
            Craft::$app->sites->getCurrentSite()->id,
            $element->getId(),
            $field->id
        );
    }

    /**
     * Gets data for a Product Details field.
     *
     * @param ProductDetails        $field   Related Field
     * @param ElementInterface|null $element Related Element
     * @param array                 $value   Data that should be used
     *                                       to populate the model
     *
     * @return ProductDetailsModel|null
     * @throws
     */
    public function getProductDetailsField($field, ElementInterface $element = null, $value = null)
    {
        if (is_array($value))
        {
            $model = new ProductDetailsModel($value);

            $model->fieldId = $field->id;
            $model->siteId  = Craft::$app->sites->getCurrentSite()->id;

            if ($element !== null)
            {
                $model->elementId = $element->getId();
            }

            return $model;
        }

        if (
            $element !== null &&
            $record = $this->_getRecord(
                Craft::$app->sites->getCurrentSite()->id,
                $element->getId(),
                $field->id
            )
        )
        {
            $model = new ProductDetailsModel($record->getAttributes());

            if ($element->getId() === null)
            {
                $model->populateDefaults();
            }

            return $model;
        }

        $productDetails = new ProductDetailsModel();

        $productDetails->fieldId = $field->id;
        $productDetails->siteId  = Craft::$app->sites->getCurrentSite()->id;

        if ($element !== null)
        {
            $productDetails->elementId = $element->getId();
        }

        $productDetails->populateDefaults();

        return $productDetails;
    }


    // Private Methods
    // =========================================================================

    /**
     * Saves the record that stores the field data.
     *
     * @param \stdClass $data       Field data to be saved
     * @param int       $siteId     Relevant Site ID
     * @param int       $elementId  Relevant Element ID
     * @param int       $fieldId    Relevant Field ID
     *
     * @return bool
     */
    private function _saveRecord($data, $siteId, $elementId, $fieldId): bool
    {
        $record = $this->_getRecord($siteId, $elementId, $fieldId);

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
     * @param int $siteId     Relevant Site ID
     * @param int $elementId  Relevant Element ID
     * @param int $fieldId    Relevant Field ID
     *
     * @return \craft\db\ActiveRecord
     */
    private function _getRecord($siteId, $elementId, $fieldId): \craft\db\ActiveRecord
    {
        $record = ProductDetailsRecord::findOne([
            'siteId'    => $siteId,
            'elementId' => $elementId,
            'fieldId'   => $fieldId
        ]);

        if ($record === null)
        {
            $record = new ProductDetailsRecord();

            $record->siteId    = $siteId;
            $record->elementId = $elementId;
            $record->fieldId   = $fieldId;
        }

        return $record;
    }

}