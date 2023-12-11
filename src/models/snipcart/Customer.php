<?php
namespace verbb\snipcart\models\snipcart;

use craft\base\Model;
use craft\helpers\UrlHelper;

use DateTime;
use stdClass;

class Customer extends Model
{
    // Constants
    // =========================================================================

    public const STATUS_CONFIRMED = 'Confirmed';
    public const STATUS_UNCONFIRMED = 'Unconfirmed';


    // Properties
    // =========================================================================

    public ?string $id = null;
    public ?string $email = null;
    public ?string $billingAddressName = null;
    public ?string $billingAddressFirstName = null;
    public ?string $billingAddressCompanyName = null;
    public ?string $billingAddressAddress1 = null;
    public ?string $billingAddressAddress2 = null;
    public ?string $billingAddressCity = null;
    public ?string $billingAddressCountry = null;
    public ?string $billingAddressProvince = null;
    public ?string $billingAddressPostalCode = null;
    public ?string $billingAddressPhone = null;
    public ?string $shippingAddressName = null;
    public ?string $shippingAddressFirstName = null;
    public ?string $shippingAddressCompanyName = null;
    public ?string $shippingAddressAddress1 = null;
    public ?string $shippingAddressAddress2 = null;
    public ?string $shippingAddressCity = null;
    public ?string $shippingAddressCountry = null;
    public ?string $shippingAddressProvince = null;
    public ?string $shippingAddressPostalCode = null;
    public ?string $shippingAddressPhone = null;
    public ?bool $shippingAddressSameAsBilling = null;
    public ?string $sessionToken = null;
    public ?string $status = null;
    public CustomerStatistics|stdClass|null $statistics = null;
    public ?string $gravatarUrl = null;
    public ?string $mode = null;
    public ?DateTime $creationDate = null;
    public ?string $gatewayId = null;
    public mixed $billingAddress = null;
    public mixed $shippingAddress = null;


    // Public Methods
    // =========================================================================

    public function getCpUrl(): string
    {
        return UrlHelper::cpUrl('snipcart/customer/' . $this->id);
    }

    public function getDashboardUrl(): ?string
    {
        if ($this->id === null) {
            return null;
        }

        return 'https://app.snipcart.com/dashboard/customers/' . $this->id;
    }

    public function extraFields(): array
    {
        return [
            'cpUrl',
        ];
    }
}
