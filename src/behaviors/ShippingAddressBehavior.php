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
 * Defines a behavior for more cleanly proxying shippingAddress* properties
 * directly on the Snipcart Order model, where they exist within a nested
 * Address object.
 *
 * @package workingconcept\snipcart\behaviors
 */
class ShippingAddressBehavior extends Behavior
{
    private $shippingAddress;

    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    /**
     * @return string|null
     */
    public function getShippingAddressName()
    {
        return $this->getShippingAddress()->name;
    }

    /**
     * @param $name
     * @return string|null
     */
    public function setShippingAddressName($name)
    {
        if ($this->shippingAddress === null) {
            $this->shippingAddress = new Address();
        }

        return $this->shippingAddress->name = $name;
    }

    /**
     * @return string|null
     */
    public function getShippingAddressFirstName()
    {
        return $this->getShippingAddress()->firstName;
    }

    /**
     * @param $firstName
     * @return string|null
     */
    public function setShippingAddressFirstName($firstName)
    {
        if ($this->shippingAddress === null) {
            $this->shippingAddress = new Address();
        }

        return $this->shippingAddress->firstName = $firstName;
    }

    /**
     * @return string|null
     */
    public function getShippingAddressCompanyName()
    {
        return $this->getShippingAddress()->companyName;
    }

    /**
     * @param $companyName
     * @return string|null
     */
    public function setShippingAddressCompanyName($companyName)
    {
        if ($this->shippingAddress === null) {
            $this->shippingAddress = new Address();
        }

        return $this->shippingAddress->companyName = $companyName;
    }

    /**
     * @return string|null
     */
    public function getShippingAddressAddress1()
    {
        return $this->getShippingAddress()->address1;
    }

    /**
     * @param $address1
     * @return string|null
     */
    public function setShippingAddressAddress1($address1)
    {
        if ($this->shippingAddress === null) {
            $this->shippingAddress = new Address();
        }

        return $this->shippingAddress->address1 = $address1;
    }

    /**
     * @return string|null
     */
    public function getShippingAddressAddress2()
    {
        return $this->getShippingAddress()->address2;
    }

    /**
     * @param $address2
     * @return string|null
     */
    public function setShippingAddressAddress2($address2)
    {
        if ($this->shippingAddress === null) {
            $this->shippingAddress = new Address();
        }

        return $this->shippingAddress->address2 = $address2;
    }

    /**
     * @return string|null
     */
    public function getShippingAddressCity()
    {
        return $this->getShippingAddress()->city;
    }

    /**
     * @param $city
     * @return string|null
     */
    public function setShippingAddressCity($city)
    {
        if ($this->shippingAddress === null) {
            $this->shippingAddress = new Address();
        }

        return $this->shippingAddress->city = $city;
    }

    /**
     * @return string|null
     */
    public function getShippingAddressCountry()
    {
        return $this->getShippingAddress()->country;
    }

    /**
     * @param $country
     * @return string|null
     */
    public function setShippingAddressCountry($country)
    {
        if ($this->shippingAddress === null) {
            $this->shippingAddress = new Address();
        }

        return $this->shippingAddress->country = $country;
    }

    /**
     * @return string|null
     */
    public function getShippingAddressProvince()
    {
        return $this->getShippingAddress()->province;
    }

    /**
     * @param $province
     * @return string|null
     */
    public function setShippingAddressProvince($province)
    {
        if ($this->shippingAddress === null) {
            $this->shippingAddress = new Address();
        }

        return $this->shippingAddress->province = $province;
    }

    /**
     * @return string|null
     */
    public function getShippingAddressPostalCode()
    {
        return $this->getShippingAddress()->postalCode;
    }

    /**
     * @param $postalCode
     * @return string
     */
    public function setShippingAddressPostalCode($postalCode)
    {
        if ($this->shippingAddress === null) {
            $this->shippingAddress = new Address();
        }

        return $this->shippingAddress->postalCode = $postalCode;
    }

    /**
     * @return string|null
     */
    public function getShippingAddressPhone()
    {
        return $this->getShippingAddress()->phone;
    }

    /**
     * @param $phone
     * @return string|null
     */
    public function setShippingAddressPhone($phone)
    {
        if ($this->shippingAddress === null) {
            $this->shippingAddress = new Address();
        }

        return $this->shippingAddress->phone = $phone;
    }
}