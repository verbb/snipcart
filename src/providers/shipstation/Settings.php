<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\providers\shipstation;

class Settings extends \craft\base\Model
{
    const ORDER_CONFIRMATION_DELIVERY = 'delivery';

    /**
     * @var string
     */
    public $apiKey;

    /**
     * @var string
     */
    public $apiSecret;

    /**
     * @var string
     */
    public $defaultCarrierCode = '';

    /**
     * @var string
     */
    public $defaultPackageCode = '';

    /**
     * @var string Two character code
     */
    public $defaultCountry;

    /**
     * @var string
     */
    public $defaultOrderConfirmation;

    /**
     * @var int
     */
    public $defaultWarehouseId;

    /**
     * @var bool
     */
    public $enableShippingRates = false;

    /**
     * @var bool
     */
    public $sendCompletedOrders = false;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['apiKey', 'apiSecret', 'defaultCountry', 'defaultWarehouseId', 'defaultOrderConfirmation'], 'required'],
            [['apiKey', 'apiSecret', 'defaultCarrierCode', 'defaultPackageCode', 'defaultOrderConfirmation'], 'string'],
            [['defaultWarehouseId'], 'number', 'integerOnly' => true],
            [['enableShippingRates', 'sendCompletedOrders'], 'boolean'],
            [['defaultCountry'], 'string', 'length' => 2],
        ];
    }

}
