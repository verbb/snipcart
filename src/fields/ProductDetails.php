<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\fields;

use workingconcept\snipcart\records\ProductDetails as ProductDetailsRecord;
use workingconcept\snipcart\models\ProductDetails as ProductDetailsModel;
use workingconcept\snipcart\assetbundles\ProductDetailsFieldAsset;
use Craft;
use craft\base\ElementInterface;

/**
 * ProductDetails
 *
 * @property ProductDetails $value
 */
class ProductDetails extends \craft\base\Field
{
    // Public Properties
    // =========================================================================

    /**
     * @var bool Whether to display "shippable" option for this field instance
     *           and allow it to be set per entry.
     */
    public $displayShippableSwitch = false;

    /**
     * @var bool Whether to display "taxable" option for this field instance
     *           and allow it to be set per entry.
     *
     */
    public $displayTaxableSwitch = false;

    /**
     * @var bool Whether to display "inventory" option for this field instance.
     */
    public $displayInventory = false;

    /**
     * @var bool Default "shippable" value.
     */
    public $defaultShippable = false;

    /**
     * @var bool Default "taxable" value.
     */
    public $defaultTaxable = false;

    /**
     * @var
     */
    public $defaultWeight;

    /**
     * @var
     */
    public $defaultWeightUnit;

    /**
     * @var
     */
    public $defaultLength;

    /**
     * @var
     */
    public $defaultWidth;

    /**
     * @var
     */
    public $defaultHeight;

    /**
     * @var
     */
    public $defaultDimensionsUnit;

    /**
     * @var string
     */
    public $skuDefault = '';


    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('snipcart', 'Snipcart Product Details');
    }

    /**
     * @return bool
     */
    public static function hasContentColumn(): bool
    {
        return false;
    }


    // Public Methods
    // =========================================================================

    /**
     * After saving element, save field to plugin table.
     *
     * @inheritdoc
     */
    public function afterElementSave(ElementInterface $element, bool $isNew)
    {
        return $this->_saveProductDetails($this, $element, $isNew);
    }

    /**
     * Prep value for use as the data leaves the database.
     *
     * @inheritdoc
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        return $this->_getProductDetails($this, $element, $value);
    }


    /**
     * Render the field itself for the control panel.
     *
     * @inheritdoc
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        Craft::$app->getView()->registerAssetBundle(
            ProductDetailsFieldAsset::class
        );

        return Craft::$app->getView()->renderTemplate(
            'snipcart/fields/product-details/field',
            [
                'name'     => $this->handle,
                'field'    => $this,
                'element'  => $element,
                'value'    => $value,
                'settings' => $this->getSettings(),
                'weightUnitOptions' => ProductDetailsModel::getWeightUnitOptions(),
                'dimensionsUnitOptions' => ProductDetailsModel::getDimensionsUnitOptions(),
            ]
        );
    }

    /**
     * Render the field's settings as it's being established.
     *
     * @inheritdoc
     */
    public function getSettingsHtml(): string
    {
        Craft::$app->getView()->registerAssetBundle(
            ProductDetailsFieldAsset::class
        );

        return Craft::$app->getView()->renderTemplate(
            'snipcart/fields/product-details/settings',
            [
                'field' => $this,
                'weightUnitOptions' => ProductDetailsModel::getWeightUnitOptions(),
                'dimensionsUnitOptions' => ProductDetailsModel::getDimensionsUnitOptions(),
            ]
        );
    }

    /**
     * Add one custom validation rule that the Element will call. This will make
     * it possible to validate each of the "sub-fields" we're working with.
     *
     * @inheritdoc
     */
    public function getElementValidationRules(): array
    {
        return [
            'validateProductDetails'
        ];
    }

    /**
     * Validate the ProductDetails model, adding any errors to the Element.
     *
     * @param ElementInterface $element
     */
    public function validateProductDetails(ElementInterface $element)
    {
        $productDetails = $element->getFieldValue($this->handle);
        $productDetails->validate();

        $errors = $productDetails->getErrors();

        if (count($errors) > 0)
        {
            foreach($errors as $subfield => $errors)
            {
                foreach($errors as $message)
                {
                    $element->addError($this->handle . '['.$subfield.']', $message);
                }
            }
        }
    }


    // Private Methods
    // =========================================================================

    /**
     * @param $field
     * @param ElementInterface $element
     * @param $isNew
     * @return bool
     * @throws
     */
    private function _saveProductDetails($field, $element, $isNew): bool
    {
        $data = $element->getFieldValue($field->handle);

        $record = $this->_getRecord(
            Craft::$app->sites->getCurrentSite()->id,
            $element->getId(),
            $field->id
        );

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
     * Get related ProductDetails.
     *
     * @param $field
     * @param ElementInterface|null $element
     * @param $value
     * @return ProductDetailsModel|null
     * @throws
     */
    private function _getProductDetails($field, ElementInterface $element = null, $value = null)
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

    /**
     * @param $siteId
     * @param $elementId
     * @param $fieldId
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
