<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models;

use craft\elements\MatrixBlock;
use workingconcept\snipcart\helpers\MeasurementHelper;
use workingconcept\snipcart\records\ProductDetails as ProductDetailsRecord;
use workingconcept\snipcart\fields\ProductDetails as ProductDetailsField;
use Craft;
use craft\helpers\Localization;
use craft\helpers\Template as TemplateHelper;

/**
 * This model is used explicitly for storing Product Details field data and
 * making some Twig functions available for convenience.
 *
 * @package workingconcept\snipcart\models
 */
class ProductDetails extends \craft\base\Model
{
    // Constants
    // =========================================================================

    const WEIGHT_UNIT_GRAMS = 'grams';
    const WEIGHT_UNIT_POUNDS = 'pounds';
    const WEIGHT_UNIT_OUNCES = 'ounces';
    const DIMENSIONS_UNIT_CENTIMETERS = 'centimeters';
    const DIMENSIONS_UNIT_INCHES = 'inches';


    // Public Properties
    // =========================================================================

    /**
     * @var
     */
    public $id;

    /**
     * @var
     */
    public $siteId;

    /**
     * @var
     */
    public $dateCreated;

    /**
     * @var
     */
    public $dateUpdated;

    /**
     * @var
     */
    public $uid;

    /**
     * @var string Unique product identifier passed on to Snipcart.
     */
    public $sku;

    /**
     * @var float Product price.
     */
    public $price;

    /**
     * @var bool Whether or not the product is something that can be shipped.
     */
    public $shippable = false;

    /**
     * @var bool Whether or not the product should be taxed.
     */
    public $taxable = false;

    /**
     * @var float Total product shipping weight.
     */
    public $weight;

    /**
     * @var string Unit that applies to provided weight.
     */
    public $weightUnit;

    /**
     * @var float Total product shipping length.
     */
    public $length;

    /**
     * @var float Total product shipping width.
     */
    public $width;

    /**
     * @var float Total product shipping height.
     */
    public $height;

    /**
     * @var int Number of items in stock or on hand.
     */
    public $inventory;

    /**
     * @var string Unit that applies to provided length, width,
     *             and height dimensions.
     */
    public $dimensionsUnit;

    /**
     * @var
     */
    public $customOptions = [];

    /**
     * @var
     */
    public $elementId;

    /**
     * @var
     */
    public $fieldId;


    // Public Methods
    // =========================================================================

    /**
     * Get the parent Element that's using the field.
     *
     * @param bool $entryOnly Whether to return the immediately-associated
     *                        Element, like a Matrix block, or the closest Entry.
     *
     * @return \craft\base\ElementInterface|null
     */
    public function getElement($entryOnly = false)
    {
        $element  = Craft::$app->elements->getElementById($this->elementId);
        $isMatrix = isset($element) && get_class($element) === MatrixBlock::class;

        if ($isMatrix && $entryOnly)
        {
            return $element->getOwner();
        }

        return $element;
    }

    /**
     * Get the relevant Field instance.
     *
     * @return \craft\base\FieldInterface|ProductDetailsField|null
     */
    public function getField()
    {
        return Craft::$app->fields->getFieldById($this->fieldId);
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            ['sku', 'validateSku'],
            [['sku', 'weightUnit', 'dimensionsUnit'], 'string'],
            [['length', 'width', 'height', 'weight'], 'number', 'integerOnly' => false],
            [['elementId', 'fieldId', 'inventory'], 'number', 'integerOnly' => true],
            [['shippable'], 'boolean'],
            [['taxable'], 'boolean'],
            [['sku'], 'required'],
            [['weightUnit'], 'in', 'range' => [
                self::WEIGHT_UNIT_GRAMS,
                self::WEIGHT_UNIT_OUNCES,
                self::WEIGHT_UNIT_POUNDS
            ]],
            [['dimensionsUnit'], 'in', 'range' => [
                self::DIMENSIONS_UNIT_CENTIMETERS,
                self::DIMENSIONS_UNIT_INCHES
            ]],
            [['weight', 'weightUnit'], 'required', 'when' => function($model){
                return $this->isShippable($model);
            }, 'message' => '{attribute} is required when product is shippable.'],
            [['length', 'width', 'height'], 'required', 'when' => function($model){
                return $this->hasDimensions($model);
            }, 'message' => '{attribute} required if there are other dimensions.'],
            [['dimensionsUnit'], 'required', 'when' => function($model){
                return $this->hasAllDimensions($model);
            }],
        ];
    }

    /**
     * Make sure that the given SKU isn't used on any record other than this one.
     * @param $attribute
     * @return bool
     */
    public function validateSku($attribute): bool
    {
        $duplicateCount = ProductDetailsRecord::find()
            ->where([$attribute => $this->{$attribute}])
            ->andWhere(['!=', 'elementId', $this->elementId])
            ->count();

        if ($duplicateCount > 0)
        {
            $this->addError($attribute, Craft::t('snipcart', 'SKU must be unique.'));
        }

        return $duplicateCount === 0;
    }

    /**
     * Gently strip out non-numeric values (commas, currency symbols, etc.)
     * before attempting to save as a decimal—then continue with the rest of the
     * validation process.
     *
     * @inheritdoc
     */
    public function beforeValidate(): bool
    {
        $this->price = $this->prepCurrencyValue($this->price);
        return parent::beforeValidate();
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
            return null;
        }

        return Localization::normalizeNumber($data);
    }

    /**
     * Set default values according to what's configured on the field instance.
     */
    public function populateDefaults()
    {
        $field = $this->getField();
        $isProductDetails = $field instanceof ProductDetailsField;

        if ($field && $isProductDetails)
        {
            $this->shippable      = $field->defaultShippable;
            $this->taxable        = $field->defaultTaxable;
            $this->weight         = $field->defaultWeight;
            $this->weightUnit     = $field->defaultWeightUnit;
            $this->length         = $field->defaultLength;
            $this->width          = $field->defaultWidth;
            $this->height         = $field->defaultHeight;
            $this->dimensionsUnit = $field->defaultDimensionsUnit;
        }
    }

    /**
     * Get weight unit options for menus.
     *
     * @return array
     */
    public static function getWeightUnitOptions(): array
    {
        return [
            self::WEIGHT_UNIT_GRAMS => 'Grams',
            self::WEIGHT_UNIT_OUNCES => 'Ounces',
            self::WEIGHT_UNIT_POUNDS => 'Pounds',
        ];
    }

    /**
     * Get dimension unit options for menus.
     *
     * @return array
     */
    public static function getDimensionsUnitOptions(): array
    {
        return [
            self::DIMENSIONS_UNIT_CENTIMETERS => 'Centimeters',
            self::DIMENSIONS_UNIT_INCHES => 'Inches',
        ];
    }

    public function getDimensionInCentimeters($dimension): float
    {
        if ($this->dimensionsUnit === self::DIMENSIONS_UNIT_INCHES)
        {
            return MeasurementHelper::inchesToCentimeters((float) $this->{$dimension});
        }

        // Already centimeters, safe to return.
        return (float) $this->{$dimension};
    }

    /**
     * Returns true if at least one dimension (length, width, height) has a
     * non-zero value.
     *
     * @param ProductDetails $model
     *
     * @return bool
     */
    public function hasDimensions($model = null): bool
    {
        $instance = $model ?? $this;
        return ! empty($instance->length) || ! empty($instance->width) || ! empty($instance->height);
    }

    /**
     * Returns true if each dimension (length, width, height) as a non-zero value.
     * @param ProductDetails $model
     * @return bool
     */
    public function hasAllDimensions($model = null): bool
    {
        $instance = $model ?? $this;
        return ! empty($instance->length) && ! empty($instance->width) && ! empty($instance->height);
    }

    /**
     * Returns true if product is shippable.
     *
     * @param ProductDetails $model
     *
     * @return bool
     */
    public function isShippable($model = null): bool
    {
        return ($model ?? $this)->shippable;
    }

    /**
     * Return the current item's weight in grams.
     *
     * @return float
     */
    public function getWeightInGrams()
    {
        if ($this->weightUnit === self::WEIGHT_UNIT_GRAMS)
        {
            // Already in grams, safe to return.
            return (float) $this->weight;
        }
        else if ($this->weightUnit === self::WEIGHT_UNIT_OUNCES)
        {
            return MeasurementHelper::ouncesToGrams((float) $this->weight);
        }
        else if ($this->weightUnit === self::WEIGHT_UNIT_POUNDS)
        {
            return MeasurementHelper::poundsToGrams((float) $this->weight);
        }
    }

    /**
     * Get markup for a "buy now" button on the public end of the site.
     *
     * @param array $params
     * @return string
     * @throws
     */
    public function getBuyNowButton($params = []): string
    {
        $params = $this->_getBuyButtonParams($params);

        return $this->_renderFieldTemplate(
            'snipcart/fields/front-end/buy-now',
            [
                'fieldData'      => $this,
                'templateParams' => $params,
            ]
        );
    }


    // Private Methods
    // =========================================================================
    
    /**
     * Configure parameters for a front-end buy button.
     * 
     * Simple options format:
     * 
     * ```
     * {{ entry.productDetails.getBuyNowButton({
     *    'customOptions': [
     *        {
     *            'name': 'Color',
     *            'required': true,
     *            'options': [ 'blue', 'green', 'red', 'pink' ]
     *        }
     *    ]
     * }) | raw }}
     * ```
     *
     * Options with price variations:
     * 
     * ```
     * {{ entry.productDetails.getBuyNowButton({
     *    'customOptions': [
     *        {
     *            'name': 'Color',
     *            'required': true,
     *            'options': [ 
     *                  {
     *                      'name': 'bronzed',
     *                      'price': 5
     *                  },
     *                  {
     *                      'name': 'diamond-studded'
     *                      'price': 500
     *                  }
     *             ]
     *        }
     *    ]
     * }) | raw }}
     * ```
     *
     * @param array $params
     * @return array
     */
    private function _getBuyButtonParams($params = []): array
    {
        $defaults = [
            'href'           => '#',
            'target'         => null,
            'rel'            => null,
            'title'          => null,
            'image'          => null,
            'text'           => 'Buy Now',
            'quantity'       => 1,
            'classes'        => ['btn'],
            'customOptions'  => [],
        ];

        $params = array_merge($defaults, $params);

        /**
         * If we have a simple array without pricing, reformat it
         * for consistency and set all prices to 0.
         */
        if (
            is_array($params['customOptions']) &&
            count($params['customOptions']) > 0
        )
        {

            foreach ($params['customOptions'] as &$customOption) 
            {
                $customOptionOptions = [];

                foreach ($customOption['options'] as $option)
                {
                    if ( ! isset($option['name']) && ! isset($option['price']))
                    {
                        $customOptionOptions[] = [
                            'name' => $option,
                            'price' => 0
                        ];
                    }
                    else
                    {
                        $customOptionOptions[] = $option;
                    }
                }

                $customOption['options'] = $customOptionOptions;
            }
        }

        return $params;
    }

    /**
     * @param $template
     * @param $data
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    private function _renderFieldTemplate($template, $data): string
    {
        $view         = Craft::$app->getView();
        $templateMode = $view->getTemplateMode();

        Craft::$app->getView()->setTemplateMode($view::TEMPLATE_MODE_CP);

        $html = Craft::$app->getView()->renderTemplate($template, $data);

        Craft::$app->getView()->setTemplateMode($templateMode);

        return TemplateHelper::raw($html);
    }

}
