<?php
namespace verbb\snipcart\models\shipstation;

use craft\base\Model;
use verbb\snipcart\models\snipcart\Package as SnipcartPackage;

class Dimensions extends Model
{
    // Static Methods
    // =========================================================================

    public static function populateFromSnipcartPackage(SnipcartPackage $snipcartPackage): self
    {
        return new self([
            'length' => $snipcartPackage->length,
            'width' => $snipcartPackage->width,
            'height' => $snipcartPackage->height,
            'units' => self::UNIT_INCHES,
        ]);
    }


    // Constants
    // =========================================================================

    public const UNIT_INCHES = 'inches';
    public const UNIT_CENTIMETERS = 'centimeters';


    // Properties
    // =========================================================================

    public ?int $length = null;
    public ?int $width = null;
    public ?int $height = null;
    public ?string $units = null;
    

    // Public Methods
    // =========================================================================

    public function rules(): array
    {
        return [
            [['length', 'width', 'height'], 'number', 'integerOnly' => true],
            [['units'], 'string'],
            [['units'], 'in', 'range' => [self::UNIT_INCHES, self::UNIT_CENTIMETERS]],
        ];
    }

    public function hasPhysicalDimensions(): bool
    {
        return $this->length !== null && $this->length > 0 && $this->width !== null && $this->width > 0 && $this->height !== null && $this->height > 0;
    }
}
