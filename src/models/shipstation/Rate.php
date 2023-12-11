<?php
namespace verbb\snipcart\models\shipstation;

/**
 * ShipStation Rate Model
 * https://www.shipstation.com/developer-api/#/reference/shipments/get-rates/get-rates
 */
class Rate extends \craft\base\Model
{
    /**
     * @var string Example: "FedEx First Overnight®"
     */
    public $serviceName;

    /**
     * @var string Example: "fedex_first_overnight"
     */
    public $serviceCode;

    /**
     * @var float
     */
    public $shipmentCost;

    /**
     * @var float
     */
    public $otherCost;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['serviceName', 'serviceCode'], 'string'],
            [['shipmentCost', 'otherCost'], 'number', 'integerOnly' => false],
        ];
    }

}
