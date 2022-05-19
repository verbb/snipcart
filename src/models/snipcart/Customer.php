<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\models\snipcart;

use craft\base\Model;
use craft\helpers\UrlHelper;
/**
 * Snipcart Customer model
 * https://docs.snipcart.com/v2/api-reference/customers
 */
class Customer extends Model
{
    public const STATUS_CONFIRMED = 'Confirmed';

    public const STATUS_UNCONFIRMED = 'Unconfirmed';

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $email;

    // TODO: use relational addresses and getters/setters to match API object like Order

    /**
     * @var string
     */
    public $billingAddressName;

    /**
     * @var string
     */
    public $billingAddressFirstName;

    /**
     * @var
     */
    public $billingAddressCompanyName;

    /**
     * @var
     */
    public $billingAddressAddress1;

    /**
     * @var
     */
    public $billingAddressAddress2;

    /**
     * @var
     */
    public $billingAddressCity;

    /**
     * @var
     */
    public $billingAddressCountry;

    /**
     * @var
     */
    public $billingAddressProvince;

    /**
     * @var
     */
    public $billingAddressPostalCode;

    /**
     * @var
     */
    public $billingAddressPhone;

    /**
     * @var
     */
    public $shippingAddressName;

    /**
     * @var
     */
    public $shippingAddressFirstName;

    /**
     * @var
     */
    public $shippingAddressCompanyName;

    /**
     * @var
     */
    public $shippingAddressAddress1;

    /**
     * @var
     */
    public $shippingAddressAddress2;

    /**
     * @var
     */
    public $shippingAddressCity;

    /**
     * @var
     */
    public $shippingAddressCountry;

    /**
     * @var
     */
    public $shippingAddressProvince;

    /**
     * @var
     */
    public $shippingAddressPostalCode;

    /**
     * @var
     */
    public $shippingAddressPhone;

    /**
     * @var bool Specifies whether the shipping and billing addresses are the same.
     */
    public $shippingAddressSameAsBilling;

    /**
     * @var string|null
     */
    public $sessionToken;

    /**
     * @var string The status of your customers, Confirmed means that the
     *             customers have created an account and Unconfirmed are those
     *             who checked out as guests.
     */
    public $status;

    /**
     * @var CustomerStatistics
     */
    public $statistics;

    /**
     * @var string
     */
    public $gravatarUrl;

    /**
     * @var
     */
    public $mode;

    /**
     * @var \DateTime
     */
    public $creationDate;

    /**
     * @var
     */
    public $gatewayId;

    /**
     * @var
     */
    public $billingAddress;

    /**
     * @var
     */
    public $shippingAddress;

    /**
     * Returns the Craft control panel URL for the detail page.
     */
    public function getCpUrl(): string
    {
        return UrlHelper::cpUrl('snipcart/customer/' . $this->id);
    }

    /**
     * Returns the URL for the customer in the Snipcart customer dashboard.
     */
    public function getDashboardUrl(): ?string
    {
        if ($this->id === null) {
            return null;
        }

        return 'https://app.snipcart.com/dashboard/customers/' . $this->id;
    }

    public function datetimeAttributes(): array
    {
        return ['creationDate'];
    }

    public function extraFields(): array
    {
        return [
            'cpUrl',
        ];
    }
}
