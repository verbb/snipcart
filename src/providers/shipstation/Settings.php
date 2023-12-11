<?php
namespace verbb\snipcart\providers\shipstation;

use craft\base\Model;
use craft\helpers\App;

class Settings extends Model
{
    // Constants
    // =========================================================================

    public const ORDER_CONFIRMATION_DELIVERY = 'delivery';


    // Properties
    // =========================================================================

    public ?string $apiKey = null;
    public ?string $apiSecret = null;
    public ?string $defaultCarrierCode = null;
    public ?string $defaultPackageCode = null;
    public ?string $defaultCountry = null;
    public ?string $defaultOrderConfirmation = null;
    public ?int $defaultWarehouseId = null;
    public bool $enableShippingRates = false;
    public bool $sendCompletedOrders = false;


    // Public Methods
    // =========================================================================

    public function getPublicKey(): string
    {
        return App::parseEnv($this->apiKey);
    }

    public function getSecretKey(): string
    {
        return App::parseEnv($this->apiSecret);
    }

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
