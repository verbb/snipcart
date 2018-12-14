<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models;

use Craft;
use craft\base\Model;

class SnipcartPackage extends Model
{
    // Constants
    // =========================================================================

    const WEIGHT_UNIT_GRAM = 'gram';
    const WEIGHT_UNIT_POUND = 'pound';
    const WEIGHT_UNIT_OUNCE = 'ounce';

    const DIMENSION_UNIT_INCH = 'inch';
    const DIMENSION_UNIT_CENTIMETER = 'centimeter';

    // Properties
    // =========================================================================

    // TODO: should dimensions be whole numbers only?

    /**
     * @var string Friendly slug for this packaging type.
     */
    public $name;

    /**
     * @var int Length of package.
     */
    public $length;

    /**
     * @var int Width of package.
     */
    public $width;

    /**
     * @var int Height of package.
     */
    public $height;

    // TODO: should weight be whole number only?

    /**
     * @var int Package weight in grams.
     */
    public $weight;

    /**
     * @var string Unit of weight measurement. (Snipcart uses grams.)
     */
    public $weightUnit = self::WEIGHT_UNIT_GRAM;

    /**
     * @var string Unit of dimension measurements.
     */
    public $dimensionUnit = self::DIMENSION_UNIT_INCH;

    // Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['length', 'width', 'height', 'weight'], 'number', 'integerOnly' => false],
            [['name'], 'string'],
            [['name', 'length', 'width', 'height', 'weight'], 'required'],
            [['weightUnit'], 'in', 'range' => [self::WEIGHT_UNIT_GRAM, self::WEIGHT_UNIT_POUND, self::WEIGHT_UNIT_OUNCE]],
            [['dimensionUnit'], 'in', 'range' => [self::DIMENSION_UNIT_INCH, self::DIMENSION_UNIT_CENTIMETER]],
        ];
    }


}
