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
    

    // Protected Methods
    // =========================================================================

    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        
        $rules[] = [['provider'], 'in', 'range' => [self::PROVIDER_CARRIER, self::PROVIDER_SHIPSURANCE]];
        $rules[] = [['insuredValue'], 'number', 'integerOnly' => false];

        return $rules;
    }
}
