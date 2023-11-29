<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\models\shipstation;

use craft\base\Model;
use fostercommerce\snipcart\models\snipcart\Item as SnipcartItem;

/**
 * ShipStation Weight Model
 * https://www.shipstation.com/developer-api/#/reference/model-weight
 */

class Weight extends Model
{
    public const UNIT_POUNDS = 'pounds';

    public const UNIT_OUNCES = 'ounces';

    public const UNIT_GRAMS = 'grams';

    /**
     * @var int Weight value.
     */
    public $value;

    /**
     * @var string Units of weight. See class constants.
     */
    public $units;

    /**
     * @var int|null (read only) A numeric value that is equivalent to the above units field.
     */
    public $WeightUnits;

    /**
     * @param SnipcartItem|\stdClass $item
     */
    public static function populateFromSnipcartItem($item): self
    {
        return new self([
            'value' => $item->weight ?? 0,
            'units' => self::UNIT_GRAMS,
        ]);
    }

    public function rules(): array
    {
        return [
            [['value'],
                'number',
                'integerOnly' => true,
            ],
            [['units'], 'string'],
            [['units'],
                'in',
                'range' => [self::UNIT_POUNDS, self::UNIT_OUNCES, self::UNIT_GRAMS],
            ],
        ];
    }
}
