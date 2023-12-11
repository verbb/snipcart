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


    // Public Methods
    // =========================================================================

    public function rules(): array
    {
        return [
            [['customsItemId', 'description', 'harmonizedTariffCode', 'countryOfOrigin'], 'string'],
            [['quantity'], 'number', 'integerOnly' => true],
            [['value'], 'number', 'integerOnly' => false],
            [['countryOfOrigin'], 'string', 'length' => 2],
        ];
    }
}
