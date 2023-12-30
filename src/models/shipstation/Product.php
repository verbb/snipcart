<?php
namespace verbb\snipcart\models\shipstation;

use craft\base\Model;

use DateTime;

class Product extends Model
{
    // Properties
    // =========================================================================

    public ?int $productId = null;
    public ?string $sku = null;
    public ?string $name = null;
    public ?float $price = null;
    public ?float $defaultCost = null;
    public ?int $length = null;
    public ?int $width = null;
    public ?int $height = null;
    public ?int $weightOz = null;
    public ?string $internalNotes = null;
    public ?string $fulfillmentSku = null;
    public ?DateTime $createDate = null;
    public ?DateTime $modifyDate = null;
    public ?bool $active = null;
    public ?ProductCategory $productCategory = null;
    public ?string $productType = null;
    public ?string $warehouseLocation = null;
    public ?string $defaultCarrierCode = null;
    public ?string $defaultServiceCode = null;
    public ?string $defaultPackageCode = null;
    public ?string $defaultIntlCarrierCode = null;
    public ?string $defaultIntlServiceCode = null;
    public ?string $defaultIntlPackageCode = null;
    public ?string $defaultConfirmation = null;
    public ?string $defaultIntlConfirmation = null;
    public ?string $customsDescription = null;
    public ?float $customsValue = null;
    public ?string $customsTariffNo = null;
    public ?string $customsCountryCode = null;
    public ?bool $noCustoms = null;
    public ?ProductTag $tags = null;


    // Protected Methods
    // =========================================================================

    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        
        $rules[] = [['productId', 'length', 'width', 'height', 'weightOz'], 'number', 'integerOnly' => true];
        $rules[] = [['price', 'defaultCost', 'customsValue'], 'number', 'integerOnly' => false];

        return $rules;
    }
}
