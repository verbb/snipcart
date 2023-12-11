<?php
namespace verbb\snipcart\models\shipstation;

use craft\base\Model;

class InsuranceOptions extends Model
{
    // Constants
    // =========================================================================

    public const PROVIDER_CARRIER = 'carrier';
    public const PROVIDER_SHIPSURANCE = 'shipsurance';


    // Properties
    // =========================================================================

    public ?string $provider = null;
    public ?bool $insureShipment = null;
    public ?int $insuredValue = 0;
    

    // Public Methods
    // =========================================================================

    public function rules(): array
    {
        return [
            [['provider'], 'string'],
            [['provider'], 'in', 'range' => [self::PROVIDER_CARRIER, self::PROVIDER_SHIPSURANCE]],
            [['insureShipment'], 'boolean'],
            [['insuredValue'], 'number', 'integerOnly' => false],
            [['insuredValue'], 'default', 'value' => 0],
        ];
    }
}
