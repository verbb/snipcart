<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\models\shipstation;

use craft\base\Model;
use fostercommerce\snipcart\models\snipcart\Address as SnipcartAddress;

/**
 * ShipStation Address Model
 * https://www.shipstation.com/developer-api/#/reference/model-address
 */
class Address extends Model
{
    public const ADDRESS_NOT_VALIDATED = 'Address not yet validated';

    public const ADDRESS_VALIDATED = 'Address validated successfully';

    public const ADDRESS_VALIDATION_WARNING = 'Address validation warning';

    public const ADDRESS_VALIDATION_FAILED = 'Address validation failed';

    /**
     * @var string|null Name of person.
     */
    public $name;

    /**
     * @var string|null Name of company.
     */
    public $company;

    /**
     * @var string|null First line of address.
     */
    public $street1;

    /**
     * @var string|null Second line of address.
     */
    public $street2;

    /**
     * @var string|null Third line of address.
     */
    public $street3;

    /**
     * @var string|null City
     */
    public $city;

    /**
     * @var string|null State
     */
    public $state;

    /**
     * @var string|null Postal Code
     */
    public $postalCode;

    /**
     * @var string Country Code. The two-character ISO country code is required.
     */
    public $country;

    /**
     * @var string|null Telephone number.
     */
    public $phone;

    /**
     * @var bool|null Specifies whether the given address is residential.
     */
    public $residential;

    /**
     * @var string|null Identifies whether the address has been verified
     *                  by ShipStation (read only). See class constants.
     */
    public $addressVerified;

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

    public function rules(): array
    {
        return [
            [['name', 'street1', 'street2', 'street3', 'city', 'state', 'postalCode', 'phone', 'addressVerified'],
                'string',
                'max' => 255,
            ],
            [['name', 'street1', 'city', 'state', 'postalCode'], 'required'],
            [['residential'], 'boolean'],
            [['company', 'street2', 'street3'],
                'default',
                'value' => null,
            ],
            [['country'],
                'default',
                'value' => 'US',
            ],
            [['country'],
                'string',
                'length' => 2,
            ],
        ];
    }
}
