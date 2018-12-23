<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\fields;

use Craft;
use craft\base\ElementInterface;

class ProductDetails extends \craft\base\Field
{
    // Constants
    // =========================================================================

    const WEIGHT_UNIT_GRAMS  = 'grams';
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
    public function rules(): array
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
     * @inheritdoc
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        return Craft::$app->getView()->renderTemplate('snipcart/fields/product-details',
            [
                'name'  => $this->handle,
                'field' => $this,
            ]
        );
    }

}