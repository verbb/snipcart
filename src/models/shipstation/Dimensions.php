<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\models\shipstation;

use craft\base\Model;
use fostercommerce\snipcart\models\snipcart\Package as SnipcartPackage;

/**
 * ShipStation Dimensions Model
 * https://www.shipstation.com/developer-api/#/reference/model-dimensions
 */
class Dimensions extends Model
{
    public const UNIT_INCHES = 'inches';

    public const UNIT_CENTIMETERS = 'centimeters';

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

    public function rules(): array
    {
        return [
            [['length', 'width', 'height'],
                'number',
                'integerOnly' => true,
            ],
            [['units'], 'string'],
            [['units'],
                'in',
                'range' => [self::UNIT_INCHES, self::UNIT_CENTIMETERS],
            ],
        ];
    }

    /**
     * Populate this model from a Package.
     */
    public static function populateFromSnipcartPackage(SnipcartPackage $snipcartPackage): self
    {
        return new self([
            'length' => $snipcartPackage->length,
            'width' => $snipcartPackage->width,
            'height' => $snipcartPackage->height,
            'units' => self::UNIT_INCHES,
        ]);
    }

    /**
     * True if valid, non-zero length, width, and height are all present.
     */
    public function hasPhysicalDimensions(): bool
    {
        return $this->length !== null && $this->length > 0 &&
            $this->width !== null && $this->width > 0 &&
            $this->height !== null && $this->height > 0;
    }
}
