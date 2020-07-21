<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models\shipstation;

use workingconcept\snipcart\models\snipcart\Package as SnipcartPackage;

/**
 * ShipStation Dimensions Model
 * https://www.shipstation.com/developer-api/#/reference/model-dimensions
 */
class Dimensions extends \craft\base\Model
{
    const UNIT_INCHES = 'inches';
    const UNIT_CENTIMETERS = 'centimeters';

    /**
     * @var int|null Length of package.
     */
    public $length;

    /**
     * @var int|null Width of package.
     */
    public $width;

    /**
     * @var int|null Height of package.
     */
    public $height;

    /**
     * @var string|null Units of measurement. See class constants.
     */
    public $units;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['length', 'width', 'height'], 'number', 'integerOnly' => true],
            [['units'], 'string'],
            [['units'], 'in', 'range' => [self::UNIT_INCHES, self::UNIT_CENTIMETERS]],
        ];
    }

    /**
     * Populate this model from a Package.
     *
     * @param SnipcartPackage $package
     * @return Dimensions
     */
    public static function populateFromSnipcartPackage(SnipcartPackage $package): Dimensions
    {
        return new self([
            'length' => $package->length,
            'width' => $package->width,
            'height' => $package->height,
            'units' => self::UNIT_INCHES,
        ]);
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
