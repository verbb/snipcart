<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models\shipstation;

/**
 * ShipStation Insurance Options Model
 * https://www.shipstation.com/developer-api/#/reference/model-insuranceoptions
 */
class InsuranceOptions extends \craft\base\Model
{
    const PROVIDER_CARRIER = 'carrier';
    const PROVIDER_SHIPSURANCE = 'shipsurance';

    /**
     * @var string|null Preferred Insurance provider.
     *                  Available options: "shipsurance" or "carrier"
     */
    public $provider;

    /**
     * @var bool|null Indicates whether shipment should be insured.
     */
    public $insureShipment;

    /**
     * @var int|null Value to insure.
     */
    public $insuredValue;

    /**
     * @inheritdoc
     */
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
