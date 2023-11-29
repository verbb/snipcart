<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\models\shipstation;

use craft\base\Model;

/**
 * ShipStation CustomsItem Model
 * https://www.shipstation.com/developer-api/#/reference/model-customsitem
 */
class CustomsItem extends Model
{
    /**
     * @var string Read Only field. When this field is not submitted in the
     *             Order/CreateOrder call, it will create a new customs line.
     *             If this field is included when submitting an order through
     *             Order/CreateOrder, then it will look for the corresponding
     *             customs line and update any values.
     */
    public $customsItemId;

    /**
     * @var string A short description of the CustomsItem
     */
    public $description;

    /**
     * @var int The quantity for this line item
     */
    public $quantity;

    /**
     * @var float The value (in USD) of the line item
     */
    public $value;

    /**
     * @var string The Harmonized Commodity Code for this line item
     */
    public $harmonizedTariffCode;

    /**
     * @var string The 2-character ISO country code where the item originated
     */
    public $countryOfOrigin;

    public function rules(): array
    {
        return [
            [['customsItemId', 'description', 'harmonizedTariffCode', 'countryOfOrigin'], 'string'],
            [['quantity'],
                'number',
                'integerOnly' => true,
            ],
            [['value'],
                'number',
                'integerOnly' => false,
            ],
            [['countryOfOrigin'],
                'string',
                'length' => 2,
            ],
        ];
    }
}
