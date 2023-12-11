<?php
namespace verbb\snipcart\behaviors;

use verbb\snipcart\models\snipcart\Address;

use yii\base\Behavior;

class ShippingAddressBehavior extends Behavior
{
    // Properties
    // =========================================================================

    private ?Address $shippingAddress = null;


    // Public Methods
    // =========================================================================

    public function getShippingAddress(): ?Address
    {
        return $this->shippingAddress;
    }

    public function getShippingAddressName(): ?string
    {
        return $this->getShippingAddress()->name;
    }

    public function setShippingAddressName(?string $name): ?string
    {
        if (!$this->shippingAddress instanceof Address) {
            $this->shippingAddress = new Address();
        }

        return $this->shippingAddress->name = $name;
    }

    public function getShippingAddressFirstName(): ?string
    {
        return $this->getShippingAddress()->firstName;
    }

    public function setShippingAddressFirstName(?string $firstName): ?string
    {
        if (!$this->shippingAddress instanceof Address) {
            $this->shippingAddress = new Address();
        }

        return $this->shippingAddress->firstName = $firstName;
    }

    public function getShippingAddressCompanyName(): ?string
    {
        return $this->getShippingAddress()->companyName;
    }

    public function setShippingAddressCompanyName(?string $companyName): ?string
    {
        if (!$this->shippingAddress instanceof Address) {
            $this->shippingAddress = new Address();
        }

        return $this->shippingAddress->companyName = $companyName;
    }

    public function getShippingAddressAddress1(): ?string
    {
        return $this->getShippingAddress()->address1;
    }

    public function setShippingAddressAddress1(?string $address1): ?string
    {
        if (!$this->shippingAddress instanceof Address) {
            $this->shippingAddress = new Address();
        }

        return $this->shippingAddress->address1 = $address1;
    }

    public function getShippingAddressAddress2(): ?string
    {
        return $this->getShippingAddress()->address2;
    }

    public function setShippingAddressAddress2(?string $address2): ?string
    {
        if (!$this->shippingAddress instanceof Address) {
            $this->shippingAddress = new Address();
        }

        return $this->shippingAddress->address2 = $address2;
    }

    public function getShippingAddressCity(): ?string
    {
        return $this->getShippingAddress()->city;
    }

    public function setShippingAddressCity(?string $city): ?string
    {
        if (!$this->shippingAddress instanceof Address) {
            $this->shippingAddress = new Address();
        }

        return $this->shippingAddress->city = $city;
    }

    public function getShippingAddressCountry(): ?string
    {
        return $this->getShippingAddress()->country;
    }

    public function setShippingAddressCountry(?string $country): ?string
    {
        if (!$this->shippingAddress instanceof Address) {
            $this->shippingAddress = new Address();
        }

        return $this->shippingAddress->country = $country;
    }

    public function getShippingAddressProvince(): ?string
    {
        return $this->getShippingAddress()->province;
    }

    public function setShippingAddressProvince(?string $province): ?string
    {
        if (!$this->shippingAddress instanceof Address) {
            $this->shippingAddress = new Address();
        }

        return $this->shippingAddress->province = $province;
    }

    public function getShippingAddressPostalCode(): ?string
    {
        return $this->getShippingAddress()->postalCode;
    }

    public function setShippingAddressPostalCode(?string $postalCode): ?string
    {
        if (!$this->shippingAddress instanceof Address) {
            $this->shippingAddress = new Address();
        }

        return $this->shippingAddress->postalCode = $postalCode;
    }

    public function getShippingAddressPhone(): ?string
    {
        return $this->getShippingAddress()->phone;
    }

    public function setShippingAddressPhone(?string $phone): ?string
    {
        if (!$this->shippingAddress instanceof Address) {
            $this->shippingAddress = new Address();
        }

        return $this->shippingAddress->phone = $phone;
    }
}
