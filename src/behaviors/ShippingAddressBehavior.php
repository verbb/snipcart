<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\behaviors;

use workingconcept\snipcart\models\Address;
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
    private $_shippingAddress;

    public function getShippingAddress()
    {
        return $this->_shippingAddress;
    }

    /**
     * @return string
     */
    public function getShippingAddressName()
    {
        return $this->getShippingAddress()->name;
    }

    /**
     * @param $name
     * @return string
     */
    public function setShippingAddressName($name)
    {
        if ($this->_shippingAddress === null)
        {
            $this->_shippingAddress = new Address();
        }

        return $this->_shippingAddress->name = $name;
    }

    /**
     * @return string
     */
    public function getShippingAddressFirstName()
    {
        return $this->getShippingAddress()->firstName;
    }

    /**
     * @param $firstName
     * @return string
     */
    public function setShippingAddressFirstName($firstName)
    {
        if ($this->_shippingAddress === null)
        {
            $this->_shippingAddress = new Address();
        }

        return $this->_shippingAddress->firstName = $firstName;
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
     * @return string
     */
    public function setShippingAddressCompanyName($companyName)
    {
        if ($this->_shippingAddress === null)
        {
            $this->_shippingAddress = new Address();
        }

        return $this->_shippingAddress->companyName = $companyName;
    }

    /**
     * @return string
     */
    public function getShippingAddressAddress1()
    {
        return $this->getShippingAddress()->address1;
    }

    /**
     * @param $address1
     * @return string
     */
    public function setShippingAddressAddress1($address1)
    {
        if ($this->_shippingAddress === null)
        {
            $this->_shippingAddress = new Address();
        }

        return $this->_shippingAddress->address1 = $address1;
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
     * @return string
     */
    public function setShippingAddressAddress2($address2)
    {
        if ($this->_shippingAddress === null)
        {
            $this->_shippingAddress = new Address();
        }

        return $this->_shippingAddress->address2 = $address2;
    }

    /**
     * @return string
     */
    public function getShippingAddressCity(): string
    {
        return $this->getShippingAddress()->city;
    }

    /**
     * @param $city
     * @return string
     */
    public function setShippingAddressCity($city)
    {
        if ($this->_shippingAddress === null)
        {
            $this->_shippingAddress = new Address();
        }

        return $this->_shippingAddress->city = $city;
    }

    /**
     * @return string
     */
    public function getShippingAddressCountry()
    {
        return $this->getShippingAddress()->country;
    }

    /**
     * @param $country
     * @return string
     */
    public function setShippingAddressCountry($country)
    {
        if ($this->_shippingAddress === null)
        {
            $this->_shippingAddress = new Address();
        }

        return $this->_shippingAddress->country = $country;
    }

    /**
     * @return string
     */
    public function getShippingAddressProvince()
    {
        return $this->getShippingAddress()->province;
    }

    /**
     * @param $province
     * @return string
     */
    public function setShippingAddressProvince($province)
    {
        if ($this->_shippingAddress === null)
        {
            $this->_shippingAddress = new Address();
        }

        return $this->_shippingAddress->province = $province;
    }

    /**
     * @return string
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
        if ($this->_shippingAddress === null)
        {
            $this->_shippingAddress = new Address();
        }

        return $this->_shippingAddress->postalCode = $postalCode;
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
     * @return string
     */
    public function setShippingAddressPhone($phone)
    {
        if ($this->_shippingAddress === null)
        {
            $this->_shippingAddress = new Address();
        }

        return $this->_shippingAddress->phone = $phone;
    }
}