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


    // Protected Methods
    // =========================================================================

    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        
        $rules[] = [['name', 'value'], 'required'];

        return $rules;
    }
}
