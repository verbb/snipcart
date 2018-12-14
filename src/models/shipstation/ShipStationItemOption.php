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
 * ShipStation Item Option Model
 * https://www.shipstation.com/developer-api/#/reference/model-itemoption
 */

class ShipStationItemOption extends Model
{
    // Properties
    // =========================================================================

    /**
     * @var string|null Name of item option. Example: "Size"
     */
    public $name;

    /**
     * @var string|null The value of the item option. Example: "Medium"
     */
    public $value;


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['name', 'value'], 'string'],
            [['name', 'value'], 'required'],
        ];
    }

}
