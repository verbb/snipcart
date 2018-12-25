<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\fields\data;

use Craft;
use craft\elements\Entry;
use workingconcept\snipcart\fields\ProductDetails;

class ProductDetailsData extends \craft\base\Model
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
     * @var Entry Reference to the related element instance.
     */
    public $element;

    /**
     * @var ProductDetails Reference to the related field instance.
     */
    public $field;

    /**
     * @var bool
     */
    public $isNew = false;


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function getElementValidationRules(): array
    {
        return [
            [['sku', 'weightUnit', 'dimensionsUnit'], 'string'],
            [['length', 'width', 'height', 'weight'], 'number', 'integerOnly' => false],
            [['shippable'], 'boolean'],
            [['taxable'], 'boolean'],
            [['sku', 'shippable'], 'required'],
            [['weight', 'weightUnit'], 'required', 'when' => function($model){
                return $model->shippable === true;
            }],
            [['weightUnit'], 'in', 'range' => [
                self::WEIGHT_UNIT_GRAMS,
                self::WEIGHT_UNIT_OUNCES,
                self::WEIGHT_UNIT_POUNDS
            ]],
            [['dimensionsUnit'], 'in', 'range' => [
                self::DIMENSIONS_UNIT_CENTIMETERS,
                self::DIMENSIONS_UNIT_INCHES
            ]],
            [['length', 'width', 'height'], 'required', 'when' => function($model){
                return $this->hasDimensions($model);
            }],
            [['dimensionsUnit'], 'required', 'when' => function($model){
                return $this->hasAllDimensions($model);
            }],
        ];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return json_encode($this);
    }

    public function populateDefaults()
    {
        // check field defaults and set them if element is new (no ID)

        if (empty($this->element->id))
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

    private function _renderFieldTemplate($template, $data): string
    {
        $view         = Craft::$app->getView();
        $templateMode = $view->getTemplateMode();

        Craft::$app->getView()->setTemplateMode($view::TEMPLATE_MODE_CP);

        $html = Craft::$app->getView()->renderTemplate($template, $data);

        Craft::$app->getView()->setTemplateMode($templateMode);

        return $html;
    }

}
