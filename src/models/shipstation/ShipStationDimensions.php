<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models;

use craft\base\Model;

/**
 * ShipStation Dimensions Model
 * https://www.shipstation.com/developer-api/#/reference/model-dimensions
 */

class ShipStationDimensions extends Model
{
    // Constants
    // =========================================================================

    const UNIT_INCHES = 'inches';
    const UNIT_CENTIMETERS = 'centimeters';


    // Properties
    // =========================================================================

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


    // Public Methods
    // =========================================================================

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
