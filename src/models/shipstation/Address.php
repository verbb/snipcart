<?php
namespace verbb\snipcart\models\shipstation;

use verbb\snipcart\models\snipcart\Address as SnipcartAddress;

use craft\base\Model;

class Address extends Model
{
    // Static Methods
    // =========================================================================

    public static function populateFromSnipcartAddress(SnipcartAddress $snipcartAddress): self
    {
        return new self([
            'name' => $snipcartAddress->name,
            'street1' => $snipcartAddress->address1,
            'street2' => $snipcartAddress->address2,
            'city' => $snipcartAddress->city,
            'state' => $snipcartAddress->province,
            'postalCode' => $snipcartAddress->postalCode,
            'country' => $snipcartAddress->country,
            'phone' => $snipcartAddress->phone,
        ]);
    }

    // Constants
    // =========================================================================

    public const ADDRESS_NOT_VALIDATED = 'Address not yet validated';
    public const ADDRESS_VALIDATED = 'Address validated successfully';
    public const ADDRESS_VALIDATION_WARNING = 'Address validation warning';
    public const ADDRESS_VALIDATION_FAILED = 'Address validation failed';


    // Properties
    // =========================================================================

    public ?string $name = null;
    public ?string $company = null;
    public ?string $street1 = null;
    public ?string $street2 = null;
    public ?string $street3 = null;
    public ?string $city = null;
    public ?string $state = null;
    public ?string $postalCode = null;
    public ?string $country = 'US';
    public ?string $phone = null;
    public ?string $residential = null;
    public ?string $addressVerified = null;


    // Protected Methods
    // =========================================================================

    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        
        $rules[] = [['name', 'street1', 'street2', 'street3', 'city', 'state', 'postalCode', 'phone', 'addressVerified'], 'string', 'max' => 255];
        $rules[] = [['name', 'street1', 'city', 'state', 'postalCode'], 'required'];
        $rules[] = [['country'], 'string', 'length' => 2];

        return $rules;
    }
}
