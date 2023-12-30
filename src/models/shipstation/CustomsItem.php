<?php
namespace verbb\snipcart\models\shipstation;

use craft\base\Model;

class CustomsItem extends Model
{
    // Properties
    // =========================================================================

    public ?string $customsItemId = null;
    public ?string $description = null;
    public ?int $quantity = null;
    public ?float $value = null;
    public ?string $harmonizedTariffCode = null;
    public ?string $countryOfOrigin = null;


    // Protected Methods
    // =========================================================================

    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        
        $rules[] = [['quantity'], 'number', 'integerOnly' => true];
        $rules[] = [['value'], 'number', 'integerOnly' => false];
        $rules[] = [['countryOfOrigin'], 'string', 'length' => 2];

        return $rules;
    }
}
