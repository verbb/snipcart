<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\fields;

use workingconcept\snipcart\fields\data\ProductDetailsData;
use workingconcept\snipcart\assetbundles\ProductDetailsFieldAsset;
use Craft;
use craft\base\ElementInterface;
use yii\db\Schema;
use craft\helpers\Localization;

/**
 * ProductDetails
 * 
 * @todo make sure every SKU is unique
 * @todo establish and honor field settings
 * @todo validate field values
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


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_TEXT;
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * @param $value
     * @return int|mixed
     */
    public function prepCurrencyValue($value)
    {
        // remove all non-numeric characters
        $data = preg_replace('/[^0-9.]/', '', $value);

        if ($data === '')
        {
            return 0;
        }

        return Localization::normalizeNumber($data);
    }

    // public function formatCurrencyValue($value)
    // {
    //     return number_format(craft()->numberFormatter->formatDecimal($value, false));
    // }

    /**
     * @inheritdoc
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        if ( ! $value instanceof ProductDetailsData)
        {
            if (is_string($value))
            {
                $value = json_decode($value);
            }

            $productDetailsData = new ProductDetailsData($value);
            $productDetailsData->element = $element;
            $productDetailsData->field = $this;
            $productDetailsData->populateDefaults();

            return $productDetailsData;
        }

        return $value;
    }

    /**
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
                'value'    => $value,
                'settings' => $this->getSettings(),
                'weightUnitOptions' => [
                    ProductDetailsData::WEIGHT_UNIT_GRAMS,
                    ProductDetailsData::WEIGHT_UNIT_OUNCES,
                    ProductDetailsData::WEIGHT_UNIT_POUNDS,
                ],
                'dimensionsUnitOptions' => [
                    ProductDetailsData::DIMENSIONS_UNIT_INCHES,
                    ProductDetailsData::DIMENSIONS_UNIT_CENTIMETERS,
                ],
            ]
        );
    }

    /**
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
                'weightUnitOptions' => [
                    ProductDetailsData::WEIGHT_UNIT_GRAMS,
                    ProductDetailsData::WEIGHT_UNIT_OUNCES,
                    ProductDetailsData::WEIGHT_UNIT_POUNDS,
                ],
                'dimensionsUnitOptions' => [
                    ProductDetailsData::DIMENSIONS_UNIT_INCHES,
                    ProductDetailsData::DIMENSIONS_UNIT_CENTIMETERS,
                ],
            ]
        );
    }

    /**
     * @param mixed                 $value
     * @param ElementInterface|null $element
     *
     * @return string
     */
    public function serializeValue($value, ElementInterface $element = null)
    {
        return json_encode($value);
    }

//    public function validate($attributeNames = null, $clearErrors = true)
//    {
//        return $this->value->validate();
//    }

}