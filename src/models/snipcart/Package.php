<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models\snipcart;

class Package extends \craft\base\Model
{
    const WEIGHT_UNIT_GRAM = 'gram';
    const WEIGHT_UNIT_POUND = 'pound';
    const WEIGHT_UNIT_OUNCE = 'ounce';

    const DIMENSION_UNIT_INCH = 'inch';
    const DIMENSION_UNIT_CENTIMETER = 'centimeter';

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

    /**
     * True if valid, non-zero length, width, and height are all present.
     *
     * @return bool
     */
    public function hasPhysicalDimensions(): bool
    {
        return $this->length !== null && $this->length > 0 &&
            $this->width !== null && $this->width > 0 &&
            $this->height !== null && $this->height > 0;
    }

}
