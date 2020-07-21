<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models\shipstation;

use workingconcept\snipcart\models\snipcart\CustomField;

/**
 * ShipStation Item Option Model
 * https://www.shipstation.com/developer-api/#/reference/model-itemoption
 */

class ItemOption extends \craft\base\Model
{
    /**
     * @var string|null Name of item option. Example: "Size"
     */
    public $name;

    /**
     * @var string|null The value of the item option. Example: "Medium"
     */
    public $value;

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

    /**
     * @param CustomField|\stdClass|array $item
     * @return ItemOption|null
     * @todo strictly use CustomField as param
     */
    public static function populateFromSnipcartCustomField($item)
    {
        if (is_array($item)) {
            $item = (object)$item;
        }

        if (isset($item->name, $item->value)) {
            return new self([
                'name'  => $item->name,
                'value' => $item->value,
            ]);
        }

        return null;
    }

}
