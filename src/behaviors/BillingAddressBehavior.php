<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\behaviors;

use workingconcept\snipcart\models\snipcart\Address;
use yii\base\Behavior;

/**
 * Defines a behavior for more cleanly proxying billingAddress* properties
 * directly on the Snipcart Order model, where they exist within a nested
 * Address object.
 *
 * @package workingconcept\snipcart\behaviors
 */
class BillingAddressBehavior extends Behavior
{
    private $billingAddress;

    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * @return string
     */
    public function getBillingAddressName()
    {
        return $this->getBillingAddress()->name;
    }

    /**
     * @param $name
     * @return string
     */
    public function setBillingAddressName($name)
    {
        if ($this->billingAddress === null) {
            $this->billingAddress = new Address();
        }

        return $this->billingAddress->name = $name;
    }

    /**
     * @return string
     */
    public function getBillingAddressFirstName()
    {
        return $this->getBillingAddress()->firstName;
    }

    /**
     * @param $firstName
     * @return string
     */
    public function setBillingAddressFirstName($firstName)
    {
        if ($this->billingAddress === null) {
            $this->billingAddress = new Address();
        }

        return $this->billingAddress->firstName = $firstName;
    }

    /**
     * @return string|null
     */
    public function getBillingAddressCompanyName()
    {
        return $this->getBillingAddress()->companyName;
    }

    /**
     * @param $companyName
     * @return string
     */
    public function setBillingAddressCompanyName($companyName)
    {
        if ($this->billingAddress === null) {
            $this->billingAddress = new Address();
        }

        return $this->billingAddress->companyName = $companyName;
    }

    /**
     * @return string
     */
    public function getBillingAddressAddress1()
    {
        return $this->getBillingAddress()->address1;
    }

    /**
     * @param $address1
     * @return string
     */
    public function setBillingAddressAddress1($address1)
    {
        if ($this->billingAddress === null) {
            $this->billingAddress = new Address();
        }

        return $this->billingAddress->address1 = $address1;
    }

    /**
     * @return string|null
     */
    public function getBillingAddressAddress2()
    {
        return $this->getBillingAddress()->address2;
    }

    /**
     * @param $address2
     * @return string
     */
    public function setBillingAddressAddress2($address2)
    {
        if ($this->billingAddress === null) {
            $this->billingAddress = new Address();
        }

        return $this->billingAddress->address2 = $address2;
    }

    /**
     * @return string
     */
    public function getBillingAddressCity(): string
    {
        return $this->getBillingAddress()->city;
    }

    /**
     * @param $city
     * @return string
     */
    public function setBillingAddressCity($city)
    {
        if ($this->billingAddress === null) {
            $this->billingAddress = new Address();
        }

        return $this->billingAddress->city = $city;
    }

    /**
     * @return string
     */
    public function getBillingAddressCountry()
    {
        return $this->getBillingAddress()->country;
    }

    /**
     * @param $country
     * @return string
     */
    public function setBillingAddressCountry($country)
    {
        if ($this->billingAddress === null) {
            $this->billingAddress = new Address();
        }

        return $this->billingAddress->country = $country;
    }

    /**
     * @return string
     */
    public function getBillingAddressProvince()
    {
        return $this->getBillingAddress()->province;
    }

    /**
     * @param $province
     * @return string
     */
    public function setBillingAddressProvince($province)
    {
        if ($this->billingAddress === null) {
            $this->billingAddress = new Address();
        }

        return $this->billingAddress->province = $province;
    }

    /**
     * @return string
     */
    public function getBillingAddressPostalCode()
    {
        return $this->getBillingAddress()->postalCode;
    }

    /**
     * @param $postalCode
     * @return string
     */
    public function setBillingAddressPostalCode($postalCode)
    {
        if ($this->billingAddress === null) {
            $this->billingAddress = new Address();
        }

        return $this->billingAddress->postalCode = $postalCode;
    }

    /**
     * @return string|null
     */
    public function getBillingAddressPhone()
    {
        return $this->getBillingAddress()->phone;
    }

    /**
     * @param $phone
     * @return string
     */
    public function setBillingAddressPhone($phone)
    {
        if ($this->billingAddress === null) {
            $this->billingAddress = new Address();
        }

        return $this->billingAddress->phone = $phone;
    }

}