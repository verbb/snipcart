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
 * Snipcart Order model
 * https://docs.snipcart.com/api-reference/orders
 *
 * @property ShipStationAddress $billingAddress
 * @property ShipStationAddress $shippingAddress
 * @property SnipcartCustomer $user
 * @property SnipcartDiscount[] $discounts
 * @property SnipcartPlan[] $plans
 * @property SnipcartItem[] $items
 */

class SnipcartOrder extends Model
{
    const PAYMENT_METHOD_CREDIT_CARD = 'CreditCard';
    const PAYMENT_STATUS_PAID = 'Paid';

    // Properties
    // =========================================================================

    /**
     * @var string
     */
    public $token;

    /**
     * @var string|null
     */
    public $parentToken;

    /**
     * @var string Date order was created. ("2018-12-05T18:37:19Z")
     */
    public $creationDate;

    /**
     * @var string Date order was last modified. ("2018-12-05T18:37:19Z")
     */
    public $modificationDate;

    /**
     * @var string
     */
    public $completionDate;

    /**
     * @var string Order status.
     */
    public $status;

    /**
     * @var
     */
    public $paymentStatus;

    /**
     * @var string
     */
    public $paymentMethod;

    /**
     * @var string
     */
    public $invoiceNumber;

    /**
     * @var string
     */
    public $parentInvoiceNumber;

    /**
     * @var string
     */
    public $email;

    /**
     * @var SnipcartCustomer
     */
    private $_user;

    /**
     * @var string
     */
    public $cardHolderName;

    /**
     * @var
     */
    public $creditCardLast4Digits;

    /**
     * @var ShipStationAddress
     */
    private $_billingAddress;

    /**
     * @var ShipStationAddress
     */
    private $_shippingAddress;

    /**
     * @var string
     */
    public $notes;

    /**
     * @var bool
     */
    public $shippingAddressSameAsBilling;

    /**
     * @var bool
     */
    public $isRecurringOrder;

    /**
     * @var float
     */
    public $finalGrandTotal;

    /**
     * @var float
     */
    public $shippingFees;

    /**
     * @var string
     */
    public $shippingMethod;

    /**
     * @var SnipcartDiscount[]
     */
    private $_discounts;

    /**
     * @var SnipcartPlan[]
     */
    private $_plans;

    /**
     * @var SnipcartItem[]
     */
    private $_items;

    /**
     * @var SnipcartRefund[]
     */
    private $_refunds;

    /**
     * @var []
     */
    public $taxes;

    /**
     * @var
     */
    public $promocodes;

    /**
     * @var bool
     */
    public $willBePaidLater;

    /**
     * @var SnipcartCustomField[]|null
     */
    public $customFields;

    /**
     * @var string|null
     */
    public $paymentTransactionId;

    /**
     * @var string|null
     */
    public $subscriptionId;

    /**
     * @var string
     */
    public $paymentGatewayUsed;

    /**
     * @var string
     */
    public $taxProvider;

    /**
     * @var string
     */
    public $lang;

    /**
     * @var bool
     */
    public $billingAddressComplete;

    /**
     * @var bool
     */
    public $shippingAddressComplete;

    /**
     * @var bool
     */
    public $shippingMethodComplete;

    /**
     * @var
     */
    public $rebateAmount;

    /**
     * @var
     */
    public $currency;

    /**
     * @var
     */
    public $recoveredFromCampaignId;

    /**
     * @var
     */
    public $trackingNumber;

    /**
     * @var
     */
    public $trackingUrl;

    /**
     * @var
     */
    public $shippingProvider;

    /**
     * @var
     */
    public $customFieldsJson;

    /**
     * @var
     */
    public $userId;

    /**
     * @var
     */
    public $cardType;

    /**
     * @var
     */
    public $refundsAmount;

    /**
     * @var
     */
    public $adjustedAmount;

    /**
     * @var
     */
    public $totalNumberOfItems;

    /**
     * @var
     */
    public $subtotal;

    /**
     * @var
     */
    public $baseTotal;

    /**
     * @var
     */
    public $itemsTotal;

    /**
     * @var
     */
    public $taxableTotal;

    /**
     * @var
     */
    public $grandTotal;

    /**
     * @var
     */
    public $total;

    /**
     * @var
     */
    public $totalWeight;

    /**
     * @var
     */
    public $totalRebateRate;

    /**
     * @var
     */
    public $shippingEnabled;

    /**
     * @var
     */
    public $numberOfItemsInOrder;

    /**
     * @var
     */
    public $metadata;

    /**
     * @var
     */
    public $taxesTotal;

    /**
     * @var
     */
    public $itemsCount;

    /**
     * @var
     */
    public $summary;

    /**
     * @var
     */
    public $ipAddress;

    /**
     * @var
     */
    public $userAgent;

    /**
     * @var
     */
    public $hasSubscriptions;



    // Public Methods
    // =========================================================================

    public function getDiscounts()
    {
        return $this->_discounts;
    }

    public function setDiscounts($discounts)
    {
        return $this->_discounts = $discounts;
    }

    public function getItems()
    {
        return $this->_items;
    }

    public function setItems($items)
    {
        return $this->_items = $items;
    }

    public function getPlans()
    {
        return $this->_plans;
    }

    public function setPlans($plans)
    {
        return $this->_plans = $plans;
    }

    public function getRefunds()
    {
        return $this->_refunds;
    }

    public function setRefunds($refunds)
    {
        return $this->_refunds = $refunds;
    }

    public function getUser()
    {
        return $this->_user;
    }

    public function setUser($user)
    {
        return $this->_user = $user;
    }

    public function getBillingAddress()
    {
        return $this->_billingAddress;
    }

    public function setBillingAddress($address)
    {
        $address = new SnipcartAddress($address);

        return $this->_billingAddress = $address;
    }

    public function getBillingAddressName()
    {
        return $this->getBillingAddress()->name;
    }

    public function setBillingAddressName($name)
    {
        if (empty($this->_billingAddress))
        {
            return $this->_billingAddress = new SnipcartAddress(['name' => $name]);
        }

        return $this->_billingAddress->name = $name;
    }

    public function getBillingAddressFirstName()
    {
        return $this->getBillingAddress()->firstName;
    }

    public function setBillingAddressFirstName($firstName)
    {
        if (empty($this->_billingAddress))
        {
            return $this->_billingAddress = new SnipcartAddress(['firstName' => $firstName]);
        }

        return $this->_billingAddress->firstName = $firstName;
    }

    public function getBillingAddressCompanyName()
    {
        return $this->getBillingAddress()->companyName;
    }

    public function setBillingAddressCompanyName($companyName)
    {
        if (empty($this->_billingAddress))
        {
            return $this->_billingAddress = new SnipcartAddress(['companyName' => $companyName]);
        }

        return $this->_billingAddress->companyName = $companyName;
    }

    public function getBillingAddressAddress1()
    {
        return $this->getBillingAddress()->address1;
    }

    public function setBillingAddressAddress1($address1)
    {
        if (empty($this->_billingAddress))
        {
            return $this->_billingAddress = new SnipcartAddress(['address1' => $address1]);
        }

        return $this->_billingAddress->address1 = $address1;
    }

    public function getBillingAddressAddress2()
    {
        return $this->getBillingAddress()->address2;
    }

    public function setBillingAddressAddress2($address2)
    {
        if (empty($this->_billingAddress))
        {
            return $this->_billingAddress = new SnipcartAddress(['address2' => $address2]);
        }

        return $this->_billingAddress->address2 = $address2;
    }

    public function getBillingAddressCity()
    {
        return $this->getBillingAddress()->city;
    }

    public function setBillingAddressCity($city)
    {
        if (empty($this->_billingAddress))
        {
            return $this->_billingAddress = new SnipcartAddress(['city' => $city]);
        }

        return $this->_billingAddress->city = $city;
    }

    public function getBillingAddressCountry()
    {
        return $this->getBillingAddress()->country;
    }

    public function setBillingAddressCountry($country)
    {
        if (empty($this->_billingAddress))
        {
            return $this->_billingAddress = new SnipcartAddress(['country' => $country]);
        }

        return $this->_billingAddress->country = $country;
    }

    public function getBillingAddressProvince()
    {
        return $this->getBillingAddress()->province;
    }

    public function setBillingAddressProvince($province)
    {
        if (empty($this->_billingAddress))
        {
            return $this->_billingAddress = new SnipcartAddress(['province' => $province]);
        }

        return $this->_billingAddress->province = $province;
    }

    public function getBillingAddressPostalCode()
    {
        return $this->getBillingAddress()->postalCode;
    }

    public function setBillingAddressPostalCode($postalCode)
    {
        if (empty($this->_billingAddress))
        {
            return $this->_billingAddress = new SnipcartAddress(['postalCode' => $postalCode]);
        }

        return $this->_billingAddress->postalCode = $postalCode;
    }

    public function getBillingAddressPhone()
    {
        return $this->getBillingAddress()->phone;
    }

    public function setBillingAddressPhone($phone)
    {
        if (empty($this->_billingAddress))
        {
            return $this->_billingAddress = new SnipcartAddress(['phone' => $phone]);
        }

        return $this->_billingAddress->phone = $phone;
    }

    public function getShippingAddress()
    {
        return $this->_shippingAddress;
    }

    public function setShippingAddress($address)
    {
        $address = new SnipcartAddress($address);

        return $this->_shippingAddress = $address;
    }

    public function getShippingAddressName()
    {
        return $this->getShippingAddress()->name;
    }

    public function setShippingAddressName($name)
    {
        if (empty($this->_shippingAddress))
        {
            return $this->_shippingAddress = new SnipcartAddress(['name' => $name]);
        }

        return $this->_shippingAddress->name = $name;
    }

    public function getShippingAddressFirstName()
    {
        return $this->getShippingAddress()->firstName;
    }

    public function setShippingAddressFirstName($firstName)
    {
        if (empty($this->_shippingAddress))
        {
            return $this->_shippingAddress = new SnipcartAddress(['firstName' => $firstName]);
        }

        return $this->_shippingAddress->firstName = $firstName;
    }

    public function getShippingAddressCompanyName()
    {
        return $this->getShippingAddress()->companyName;
    }

    public function setShippingAddressCompanyName($companyName)
    {
        if (empty($this->_shippingAddress))
        {
            return $this->_shippingAddress = new SnipcartAddress(['companyName' => $companyName]);
        }

        return $this->_shippingAddress->companyName = $companyName;
    }

    public function getShippingAddressAddress1()
    {
        return $this->getShippingAddress()->address1;
    }

    public function setShippingAddressAddress1($address1)
    {
        if (empty($this->_shippingAddress))
        {
            return $this->_shippingAddress = new SnipcartAddress(['address1' => $address1]);
        }

        return $this->_shippingAddress->address1 = $address1;
    }

    public function getShippingAddressAddress2()
    {
        return $this->getShippingAddress()->address2;
    }

    public function setShippingAddressAddress2($address2)
    {
        if (empty($this->_shippingAddress))
        {
            return $this->_shippingAddress = new SnipcartAddress(['address2' => $address2]);
        }

        return $this->_shippingAddress->address2 = $address2;
    }

    public function getShippingAddressCity()
    {
        return $this->getShippingAddress()->city;
    }

    public function setShippingAddressCity($city)
    {
        if (empty($this->_shippingAddress))
        {
            return $this->_shippingAddress = new SnipcartAddress(['city' => $city]);
        }

        return $this->_shippingAddress->city = $city;
    }

    public function getShippingAddressCountry()
    {
        return $this->getShippingAddress()->country;
    }

    public function setShippingAddressCountry($country)
    {
        if (empty($this->_shippingAddress))
        {
            return $this->_shippingAddress = new SnipcartAddress(['country' => $country]);
        }

        return $this->_shippingAddress->country = $country;
    }

    public function getShippingAddressProvince()
    {
        return $this->getShippingAddress()->province;
    }

    public function setShippingAddressProvince($province)
    {
        if (empty($this->_shippingAddress))
        {
            return $this->_shippingAddress = new SnipcartAddress(['province' => $province]);
        }

        return $this->_shippingAddress->province = $province;
    }

    public function getShippingAddressPostalCode()
    {
        return $this->getShippingAddress()->postalCode;
    }

    public function setShippingAddressPostalCode($postalCode)
    {
        if (empty($this->_shippingAddress))
        {
            return $this->_shippingAddress = new SnipcartAddress(['postalCode' => $postalCode]);
        }

        return $this->_shippingAddress->postalCode = $postalCode;
    }

    public function getShippingAddressPhone()
    {
        return $this->getShippingAddress()->phone;
    }

    public function setShippingAddressPhone($phone)
    {
        if (empty($this->_shippingAddress))
        {
            return $this->_shippingAddress = new SnipcartAddress(['phone' => $phone]);
        }

        return $this->_shippingAddress->phone = $phone;
    }


    /**
     * @inheritdoc
     */

    public function rules()
    {
        return [
            [[
                'token',
                'parentToken',
                'parentInvoiceNumber',
                'creationDate',
                'modificationDate',
                'status',
                'paymentMethod',
                'invoiceNumber',
                'parentInvoiceNumber',
                'email',
                'cardHolderName',
                'creditCardLast4Digits',
                'notes',
                'billingAddressName',
                'billingAddressFirstName',
                'billingAddressCompanyName',
                'billingAddressAddress1',
                'billingAddressAddress2',
                'billingAddressCity',
                'billingAddressCountry',
                'billingAddressProvince',
                'billingAddressPostalCode',
                'billingAddressPhone',
                'shippingAddressName',
                'shippingAddressCompanyName',
                'shippingAddressAddress1',
                'shippingAddressAddress2',
                'shippingAddressCity',
                'shippingAddressCountry',
                'shippingAddressProvince',
                'shippingAddressPostalCode',
                'shippingAddressPhone',
                'shippingMethod',
                'shippingMethod',
                'subscriptionId',
                'paymentTransactionId',
                'paymentGatewayUsed',
                'taxProvider',
                'lang',
                'ipAddress',
                'userAgent',
                'currency',
                'recoveredFromCampaignId',
                'trackingNumber',
                'trackingUrl',
             ], 'string'],
            [[
                'shippingAddressSameAsBilling',
                'willBePaidLater',
                'billingAddressComplete',
                'shippingAddressComplete',
                'shippingMethodComplete',
                'isRecurringOrder'
             ], 'boolean'],
            //[[], 'number', 'integerOnly' => true],
            [['finalGrandTotal', 'shippingFees', 'rebateAmount'], 'number', 'integerOnly' => false],

        ];
    }
}
