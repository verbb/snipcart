<?php
namespace verbb\snipcart\behaviors;

use verbb\snipcart\models\snipcart\Address;

use yii\base\Behavior;

class BillingAddressBehavior extends Behavior
{
    // Properties
    // =========================================================================

    private ?Address $billingAddress = null;


    // Public Methods
    // =========================================================================

    public function getBillingAddress(): ?Address
    {
        return $this->billingAddress;
    }

    public function getBillingAddressName(): ?string
    {
        return $this->getBillingAddress()->name;
    }

    public function setBillingAddressName(?string $name): ?string
    {
        if (!$this->billingAddress instanceof Address) {
            $this->billingAddress = new Address();
        }

        return $this->billingAddress->name = $name;
    }

    public function getBillingAddressFirstName(): ?string
    {
        return $this->getBillingAddress()->firstName;
    }

    public function setBillingAddressFirstName(?string $firstName): ?string
    {
        if (!$this->billingAddress instanceof Address) {
            $this->billingAddress = new Address();
        }

        return $this->billingAddress->firstName = $firstName;
    }

    public function getBillingAddressCompanyName(): ?string
    {
        return $this->getBillingAddress()->companyName;
    }

    public function setBillingAddressCompanyName(?string $companyName): ?string
    {
        if (!$this->billingAddress instanceof Address) {
            $this->billingAddress = new Address();
        }

        return $this->billingAddress->companyName = $companyName;
    }

    public function getBillingAddressAddress1(): ?string
    {
        return $this->getBillingAddress()->address1;
    }

    public function setBillingAddressAddress1(?string $address1): ?string
    {
        if (!$this->billingAddress instanceof Address) {
            $this->billingAddress = new Address();
        }

        return $this->billingAddress->address1 = $address1;
    }

    public function getBillingAddressAddress2(): ?string
    {
        return $this->getBillingAddress()->address2;
    }

    public function setBillingAddressAddress2(?string $address2): ?string
    {
        if (!$this->billingAddress instanceof Address) {
            $this->billingAddress = new Address();
        }

        return $this->billingAddress->address2 = $address2;
    }

    public function getBillingAddressCity(): ?string
    {
        return $this->getBillingAddress()->city;
    }

    public function setBillingAddressCity(?string $city): ?string
    {
        if (!$this->billingAddress instanceof Address) {
            $this->billingAddress = new Address();
        }

        return $this->billingAddress->city = $city;
    }

    public function getBillingAddressCountry(): ?string
    {
        return $this->getBillingAddress()->country;
    }

    public function setBillingAddressCountry(?string $country): ?string
    {
        if (!$this->billingAddress instanceof Address) {
            $this->billingAddress = new Address();
        }

        return $this->billingAddress->country = $country;
    }

    public function getBillingAddressProvince(): ?string
    {
        return $this->getBillingAddress()->province;
    }

    public function setBillingAddressProvince(?string $province): ?string
    {
        if (!$this->billingAddress instanceof Address) {
            $this->billingAddress = new Address();
        }

        return $this->billingAddress->province = $province;
    }

    public function getBillingAddressPostalCode(): ?string
    {
        return $this->getBillingAddress()->postalCode;
    }

    public function setBillingAddressPostalCode(?string $postalCode): ?string
    {
        if (!$this->billingAddress instanceof Address) {
            $this->billingAddress = new Address();
        }

        return $this->billingAddress->postalCode = $postalCode;
    }

    public function getBillingAddressPhone(): ?string
    {
        return $this->getBillingAddress()->phone;
    }

    public function setBillingAddressPhone(?string $phone): ?string
    {
        if (!$this->billingAddress instanceof Address) {
            $this->billingAddress = new Address();
        }

        return $this->billingAddress->phone = $phone;
    }
}
