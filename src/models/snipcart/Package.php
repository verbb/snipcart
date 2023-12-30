<?php
namespace verbb\snipcart\models\snipcart;

use craft\base\Model;

class Package extends Model
{
    // Constants
    // =========================================================================

    public const WEIGHT_UNIT_GRAM = 'gram';
    public const WEIGHT_UNIT_POUND = 'pound';
    public const WEIGHT_UNIT_OUNCE = 'ounce';
    public const DIMENSION_UNIT_INCH = 'inch';
    public const DIMENSION_UNIT_CENTIMETER = 'centimeter';


    // Properties
    // =========================================================================

    public ?string $name = null;
    public ?int $length = null;
    public ?int $width = null;
    public ?int $height = null;
    public ?int $weight = null;
    public ?string $weightUnit = self::WEIGHT_UNIT_GRAM;
    public ?string $dimensionUnit = self::DIMENSION_UNIT_INCH;


    // Public Methods
    // =========================================================================

    public function hasPhysicalDimensions(): bool
    {
        return $this->length !== null && $this->length > 0 && $this->width !== null && $this->width > 0 && $this->height !== null && $this->height > 0;
    }
    

    // Protected Methods
    // =========================================================================

    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        
        $rules[] = [['length', 'width', 'height', 'weight'], 'number', 'integerOnly' => false];
        $rules[] = [['name', 'length', 'width', 'height', 'weight'], 'required'];
        $rules[] = [['weightUnit'], 'in', 'range' => [self::WEIGHT_UNIT_GRAM, self::WEIGHT_UNIT_POUND, self::WEIGHT_UNIT_OUNCE]];
        $rules[] = [['dimensionUnit'], 'in', 'range' => [self::DIMENSION_UNIT_INCH, self::DIMENSION_UNIT_CENTIMETER]];

        return $rules;
    }
}
