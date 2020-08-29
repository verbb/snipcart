<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models\snipcart;

/**
 * Class Address
 *
 * @package workingconcept\snipcart\models
 */
class Address extends \craft\base\Model
{
    /**
     * @var string Full name of addressee.
     */
    public $name;

    /**
     * @var string First name of addressee.
     */
    public $firstName;

    /**
     * @var string Full name of addressee.
     */
    public $fullName;

    /**
     * @var string|null Company name.
     */
    public $companyName;

    /**
     * @var string|null Company name.
     */
    public $company;

    /**
     * @var string First line of address.
     */
    public $address1;

    /**
     * @var string|null Second line of address.
     */
    public $address2;

    /**
     * @var string|null Full address.
     */
    public $fullAddress;

    /**
     * @var string Name of city.
     */
    public $city;

    /**
     * @var string Two-character abbreviation for country.
     */
    public $country;

    /**
     * @var string Two-character abbreviation for province or U.S. state.
     */
    public $province;

    /**
     * @var string Postal code number.
     */
    public $postalCode;

    /**
     * @var string|null Associated phone number.
     */
    public $phone;

    /**
     * @var string|null VAT number.
     */
    public $vatNumber;

    /**
     * @var string|null Used only via Settings->shipFrom; not part of Snipcart’s own API.
     */
    public $email;

    /**
     * @var bool
     */
    public $hasMinimalRequiredInfo;

    /**
     * @return string|null
     */
    public function getFormattedPhone()
    {
        $num = $this->phone;

        if (strlen($num) === 10 && is_numeric($num)) {
            // format US phone numbers (555) 555-5555
            return ($num) ?
                '('.substr($num,0,3).') '.substr($num,3,3).'-'.substr($num,6,4)
                : '&nbsp;';
        }

        return $num;
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['name', 'companyName', 'address1', 'address2', 'city', 'country', 'province', 'postalCode', 'phone', 'email'], 'string', 'max' => 255],
            [['name', 'address1', 'city', 'country', 'province', 'postalCode'], 'required'],
            [['companyName', 'address2', 'phone'], 'default', 'value' => null],
            [['country'], 'default', 'value' => 'US'],
            [['country', 'province'], 'string', 'length' => 2],
        ];
    }

}
