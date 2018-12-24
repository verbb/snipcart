<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\fields\data;

use Craft;

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
     * @var 
     */
    public $element;


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

    public function __toString()
    {

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
            return floatval($this->weight);
        }
        else if ($this->weightUnit === self::WEIGHT_UNIT_OUNCES)
        {
            return floatval($this->weight) * 28.3495;
        }
        else if ($this->weightUnit === self::WEIGHT_UNIT_POUNDS)
        {
            return floatval($this->weight) * 453.592;
        }
    }

    /**
     * Get markup for a "buy now" button on the public end of the site.
     *
     * @param array $params
     * @return string
     */
    public function getBuyNowButton($params = [])
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

    public function getAddToCartButton()
    {
        // TODO: include quantity
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
     * @return void
     */
    private function _getBuyButtonParams($params = [])
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
