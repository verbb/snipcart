<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models;

use craft\base\Model;

/**
 * Snipcart Customer model
 * https://docs.snipcart.com/api-reference/customers
 */
class SnipcartCustomer extends Model
{
    // Constants
    // =========================================================================

    const STATUS_CONFIRMED = 'Confirmed';
    const STATUS_UNCONFIRMED = 'Unconfirmed';


    // Properties
    // =========================================================================

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $email;

    // TODO: use relational addresses and getters/setters to match API object like SnipcartOrder

    /**
     * @var string
     */
    public $billingAddressName;

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
     * @var bool Specifies whether the shipping and bililng addresses are the same.
     */
    public $shippingAddressSameAsBilling;

    /**
     * @var string|null
     */
    public $sessionToken;

    /**
     * @var string The status of your customers, Confirmed means that the customers have created an account and Unconfirmed are those who checked out as guests.
     */
    public $status;

    /**
     * @var SnipcartCustomerStatistics
     */
    public $statistics;

    /**
     * @var
     */
    public $gravatarUrl;


    // Public Methods
    // =========================================================================

}
