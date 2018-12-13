<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models;

use Craft;
use craft\base\Model;

/**
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

    // TODO: can these be related models that are flattened again?
  // maybe just getter and setter interfaces that interact with models?
    /**
     * @var string
     */
    public $billingAddressName;
    public $billingAddressCompanyName;
    public $billingAddressAddress1;
    public $billingAddressAddress2;
    public $billingAddressCity;
    public $billingAddressCountry;
    public $billingAddressProvince;
    public $billingAddressPostalCode;
    public $billingAddressPhone;
    
    public $shippingAddressName;
    public $shippingAddressCompanyName;
    public $shippingAddressAddress1;
    public $shippingAddressAddress2;
    public $shippingAddressCity;
    public $shippingAddressCountry;
    public $shippingAddressProvince;
    public $shippingAddressPostalCode;
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

    public $gravatarUrl;


    // Public Methods
    // =========================================================================

}
