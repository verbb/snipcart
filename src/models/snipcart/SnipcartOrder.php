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
 * Snipcart Order model
 * https://docs.snipcart.com/api-reference/orders
 *
 * @package workingconcept\snipcart\models
 *
 * @property ShipStationAddress $billingAddress
 * @property ShipStationAddress $shippingAddress
 * @property SnipcartCustomer $user
 * @property SnipcartDiscount[] $discounts
 * @property SnipcartPlan[] $plans
 * @property SnipcartItem[] $items
 * @property string $billingAddressName;
 * @property string $billingAddressFirstName;
 * @property string $billingAddressCompanyName;
 * @property string $billingAddressAddress1;
 * @property string $billingAddressAddress2;
 * @property string $billingAddressCity;
 * @property string $billingAddressCountry;
 * @property string $billingAddressProvince;
 * @property string $billingAddressPostalCode;
 * @property string $billingAddressPhone;
 * @property string $shippingAddressName;
 * @property string $shippingAddressFirstName;
 * @property string $shippingAddressCompanyName;
 * @property string $shippingAddressAddress1;
 * @property string $shippingAddressAddress2;
 * @property string $shippingAddressCity;
 * @property string $shippingAddressCountry;
 * @property string $shippingAddressProvince;
 * @property string $shippingAddressPostalCode;
 * @property string $shippingAddressPhone;
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
     * @var SnipcartAddress
     */
    private $_billingAddress;

    /**
     * @var SnipcartAddress
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
     * @var array
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

    /**
     * @return array
     */
    public function getDiscounts(): array
    {
        return $this->_discounts;
    }

    /**
     * @param $discounts
     * @return mixed
     */
    public function setDiscounts($discounts)
    {
        return $this->_discounts = $discounts;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->_items;
    }

    /**
     * @param $items
     * @return mixed
     */
    public function setItems($items)
    {
        return $this->_items = $items;
    }

    /**
     * @return array
     */
    public function getPlans(): array
    {
        return $this->_plans;
    }

    /**
     * @param $plans
     * @return mixed
     */
    public function setPlans($plans)
    {
        return $this->_plans = $plans;
    }

    /**
     * @return array
     */
    public function getRefunds(): array
    {
        return $this->_refunds;
    }

    /**
     * @param $refunds
     * @return mixed
     */
    public function setRefunds($refunds)
    {
        return $this->_refunds = $refunds;
    }

    /**
     * @return SnipcartCustomer
     */
    public function getUser(): SnipcartCustomer
    {
        return $this->_user;
    }

    /**
     * @param $user
     * @return mixed
     */
    public function setUser($user)
    {
        return $this->_user = $user;
    }

    /**
     * @return SnipcartAddress
     */
    public function getBillingAddress(): SnipcartAddress
    {
        return $this->_billingAddress;
    }

    /**
     * @param SnipcartAddress|array $address
     * @return SnipcartAddress
     */
    public function setBillingAddress($address): SnipcartAddress
    {
        if ( ! $address instanceof SnipcartAddress)
        {
            $address = new SnipcartAddress($address);
        }

        return $this->_billingAddress = $address;
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
        if ($this->_billingAddress === null)
        {
            $this->_billingAddress = new SnipcartAddress();
        }

        return $this->_billingAddress->name = $name;
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
        if ($this->_billingAddress === null)
        {
            $this->_billingAddress = new SnipcartAddress();
        }

        return $this->_billingAddress->firstName = $firstName;
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
        if ($this->_billingAddress === null)
        {
            $this->_billingAddress = new SnipcartAddress();
        }

        return $this->_billingAddress->companyName = $companyName;
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
        if ($this->_billingAddress === null)
        {
            $this->_billingAddress = new SnipcartAddress();
        }

        return $this->_billingAddress->address1 = $address1;
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
        if ($this->_billingAddress === null)
        {
            $this->_billingAddress = new SnipcartAddress();
        }

        return $this->_billingAddress->address2 = $address2;
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
        if ($this->_billingAddress === null)
        {
            $this->_billingAddress = new SnipcartAddress();
        }

        return $this->_billingAddress->city = $city;
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
        if ($this->_billingAddress === null)
        {
            $this->_billingAddress = new SnipcartAddress();
        }

        return $this->_billingAddress->country = $country;
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
        if ($this->_billingAddress === null)
        {
            $this->_billingAddress = new SnipcartAddress();
        }

        return $this->_billingAddress->province = $province;
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
        if ($this->_billingAddress === null)
        {
            $this->_billingAddress = new SnipcartAddress();
        }

        return $this->_billingAddress->postalCode = $postalCode;
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
        if ($this->_billingAddress === null)
        {
            $this->_billingAddress = new SnipcartAddress();
        }

        return $this->_billingAddress->phone = $phone;
    }

    /**
     * @return SnipcartAddress
     */
    public function getShippingAddress(): SnipcartAddress
    {
        return $this->_shippingAddress;
    }

    /**
     * @param SnipcartAddress|array $address
     * @return SnipcartAddress
     */
    public function setShippingAddress($address): SnipcartAddress
    {
        if ( ! $address instanceof SnipcartAddress)
        {
            $address = new SnipcartAddress($address);
        }

        return $this->_shippingAddress = $address;
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
            $this->_shippingAddress = new SnipcartAddress();
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
            $this->_shippingAddress = new SnipcartAddress();
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
            $this->_shippingAddress = new SnipcartAddress();
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
            $this->_shippingAddress = new SnipcartAddress();
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
            $this->_shippingAddress = new SnipcartAddress();
        }

        return $this->_shippingAddress->address2 = $address2;
    }

    /**
     * @return string
     */
    public function getShippingAddressCity()
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
            $this->_shippingAddress = new SnipcartAddress();
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
            $this->_shippingAddress = new SnipcartAddress();
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
            $this->_shippingAddress = new SnipcartAddress();
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
            $this->_shippingAddress = new SnipcartAddress();
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
            $this->_shippingAddress = new SnipcartAddress();
        }

        return $this->_shippingAddress->phone = $phone;
    }


    /**
     * @inheritdoc
     */
    public function rules(): array
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
            [['finalGrandTotal', 'shippingFees', 'rebateAmount'], 'number', 'integerOnly' => false],
        ];
    }
}
