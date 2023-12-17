<?php
namespace verbb\snipcart\models\shipstation;

use verbb\snipcart\models\snipcart\CustomField;

use craft\base\Model;

use stdClass;

class ItemOption extends Model
{
    // Static Methods
    // =========================================================================

    public static function populateFromSnipcartCustomField(array|CustomField|stdClass $item): ?self
    {
        if (is_array($item)) {
            $item = (object)$item;
        }

        if (isset($item->name, $item->value)) {
            return new self([
                'name' => $item->name,
                'value' => $item->value,
            ]);
        }

        return null;
    }


    // Properties
    // =========================================================================

    public ?string $name = null;
    public ?string $value = null;


    // Public Methods
    // =========================================================================

    public function rules(): array
    {
        return [
            [['name', 'value'], 'string'],
            [['name', 'value'], 'required'],
        ];
    }
}
