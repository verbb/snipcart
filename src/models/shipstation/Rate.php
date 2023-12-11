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
    

    // Public Methods
    // =========================================================================

    public function rules(): array
    {
        return [
            [['serviceName', 'serviceCode'], 'string'],
            [['shipmentCost', 'otherCost'], 'number', 'integerOnly' => false],
        ];
    }
}
