<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models;

use workingconcept\snipcart\records\ProductDetails as ProductDetailsRecord;
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
     * @return \craft\base\ElementInterface|null
     */
    public function getElement()
    {
        return Craft::$app->elements->getElementById($this->elementId);
    }

    /**
     * Get the relevant Field instance.
     *
     * @return \craft\base\FieldInterface|null
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
            [['elementId', 'fieldId'], 'number', 'integerOnly' => true],
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
        $existingRecord = ProductDetailsRecord::find()
            ->where([$attribute => $this->{$attribute}])
            ->andWhere(['!=', 'elementId', $this->elementId])
            ->one();

        if ($existingRecord !== null)
        {
            $this->addError($attribute, Craft::t('snipcart', 'SKU must be unique.'));
        }

        return $existingRecord === null;
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
     * Set defaults according to each configured field instance.
     */
    public function populateDefaults()
    {
        $this->shippable = $this->field->defaultShippable;
        $this->taxable = $this->field->defaultTaxable;
        $this->weight = $this->field->defaultWeight;
        $this->weightUnit = $this->field->defaultWeightUnit;
        $this->length = $this->field->defaultLength;
        $this->width = $this->field->defaultWidth;
        $this->height = $this->field->defaultHeight;
        $this->dimensionsUnit = $this->field->defaultDimensionsUnit;
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

    /**
     * Returns true if at least one dimension (length, width, height) has a
     * non-zero value.
     *
     * @param null $model
     * @return bool
     */
    public function hasDimensions($model = null): bool
    {
        $instance = $model ?? $this;
        return ! empty($instance->length) || ! empty($instance->width) || ! empty($instance->height);
    }

    /**
     * Returns true if each dimension (length, width, height) as a non-zero value.
     * @param null $model
     * @return bool
     */
    public function hasAllDimensions($model = null): bool
    {
        $instance = $model ?? $this;
        return ! empty($instance->length) && ! empty($instance->width) && ! empty($instance->height);
    }

    /**
     * @param null $model
     * @return bool
     */
    public function isShippable($model = null): bool
    {
        $instance = $model ?? $this;
        return $instance->shippable;
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
            return (float) $this->weight;
        }
        else if ($this->weightUnit === self::WEIGHT_UNIT_OUNCES)
        {
            return (float) $this->weight * 28.3495;
        }
        else if ($this->weightUnit === self::WEIGHT_UNIT_POUNDS)
        {
            return (float) $this->weight * 453.592;
        }
    }

    /**
     * Get markup for a "buy now" button on the public end of the site.
     *
     * @param array $params
     * @return string
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
            'class'          => 'btn snipcart-add-item',
            'text'           => 'Buy Now',
            'quantity'       => 1,
            'customOptions'  => [],
        ];

        $params = array_merge($defaults, $params);

        /**
         * If we have a simple array without pricing, reformat it
         * for consistency and set all prices to 0.
         */
        if (count($params['customOptions']) > 0)
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
