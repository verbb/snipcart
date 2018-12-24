<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\fields;

use workingconcept\snipcart\fields\data\ProductDetailsData;
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

    public $displayShippableSwitch = false;
    public $displayTaxableSwitch = false;
    public $shippableDefault = false;
    public $taxableDefault = false;
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

    public function prepCurrencyValue($value)
    {
        // remove all non-numeric characters
        $data = preg_replace('/[^0-9.]/', '', $value);

        if ($data === '')
        {
            return 0;
        }
        else
        {
            return Localization::normalizeNumber($data);
        }
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
            $valueData = json_decode($value);
            $valueData->element = $element;
            return new ProductDetailsData($valueData);
        }

        return $value;
    }

    /**
     * @inheritdoc
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        return Craft::$app->getView()->renderTemplate('snipcart/fields/product-details/field',
            [
                'name' => $this->handle,
                'field' => $this,
                'value' => $value,
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

    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate(
            'snipcart/fields/product-details/settings',
            [
                'field' => $this,
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

}