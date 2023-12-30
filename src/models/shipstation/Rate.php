<?php
namespace verbb\snipcart\models\shipstation;

use craft\base\Model;

class Rate extends Model
{
    // Properties
    // =========================================================================

    public ?string $serviceName = null;
    public ?string $serviceCode = null;
    public ?float $shipmentCost = null;
    public ?float $otherCost = null;
    

    // Protected Methods
    // =========================================================================

    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        
        $rules[] = [['shipmentCost', 'otherCost'], 'number', 'integerOnly' => false];

        return $rules;
    }
}
