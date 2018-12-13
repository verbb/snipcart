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
     * @param $address
     * @return SnipcartAddress
     */
    public function setBillingAddress($address): SnipcartAddress
    {
        $address = new SnipcartAddress($address);

        return $this->_billingAddress = $address;
    }

    /**
     * @return string
     */
    public function getBillingAddressName(): string
    {
        return $this->getBillingAddress()->name;
    }

    /**
     * @param $name
     * @return SnipcartAddress
     */
    public function setBillingAddressName($name): SnipcartAddress
    {
        if ($this->_billingAddress === null)
        {
            return $this->_billingAddress = new SnipcartAddress(['name' => $name]);
        }

        return $this->_billingAddress->name = $name;
    }

    /**
     * @return string
     */
    public function getBillingAddressFirstName(): string
    {
        return $this->getBillingAddress()->firstName;
    }

    /**
     * @param $firstName
     * @return SnipcartAddress
     */
    public function setBillingAddressFirstName($firstName): SnipcartAddress
    {
        if ($this->_billingAddress === null)
        {
            return $this->_billingAddress = new SnipcartAddress(['firstName' => $firstName]);
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
     * @return SnipcartAddress
     */
    public function setBillingAddressCompanyName($companyName): SnipcartAddress
    {
        if ($this->_billingAddress === null)
        {
            return $this->_billingAddress = new SnipcartAddress(['companyName' => $companyName]);
        }

        return $this->_billingAddress->companyName = $companyName;
    }

    /**
     * @return string
     */
    public function getBillingAddressAddress1(): string
    {
        return $this->getBillingAddress()->address1;
    }

    /**
     * @param $address1
     * @return SnipcartAddress
     */
    public function setBillingAddressAddress1($address1): SnipcartAddress
    {
        if ($this->_billingAddress === null)
        {
            return $this->_billingAddress = new SnipcartAddress(['address1' => $address1]);
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
     * @return SnipcartAddress
     */
    public function setBillingAddressAddress2($address2): SnipcartAddress
    {
        if ($this->_billingAddress === null)
        {
            return $this->_billingAddress = new SnipcartAddress(['address2' => $address2]);
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
     * @return SnipcartAddress
     */
    public function setBillingAddressCity($city): SnipcartAddress
    {
        if ($this->_billingAddress === null)
        {
            return $this->_billingAddress = new SnipcartAddress(['city' => $city]);
        }

        return $this->_billingAddress->city = $city;
    }

    /**
     * @return string
     */
    public function getBillingAddressCountry(): string
    {
        return $this->getBillingAddress()->country;
    }

    /**
     * @param $country
     * @return SnipcartAddress
     */
    public function setBillingAddressCountry($country): SnipcartAddress
    {
        if ($this->_billingAddress === null)
        {
            return $this->_billingAddress = new SnipcartAddress(['country' => $country]);
        }

        return $this->_billingAddress->country = $country;
    }

    /**
     * @return string
     */
    public function getBillingAddressProvince(): string
    {
        return $this->getBillingAddress()->province;
    }

    /**
     * @param $province
     * @return SnipcartAddress
     */
    public function setBillingAddressProvince($province): SnipcartAddress
    {
        if ($this->_billingAddress === null)
        {
            return $this->_billingAddress = new SnipcartAddress(['province' => $province]);
        }

        return $this->_billingAddress->province = $province;
    }

    /**
     * @return string
     */
    public function getBillingAddressPostalCode(): string
    {
        return $this->getBillingAddress()->postalCode;
    }

    /**
     * @param $postalCode
     * @return SnipcartAddress
     */
    public function setBillingAddressPostalCode($postalCode): SnipcartAddress
    {
        if ($this->_billingAddress === null)
        {
            return $this->_billingAddress = new SnipcartAddress(['postalCode' => $postalCode]);
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
     * @return SnipcartAddress
     */
    public function setBillingAddressPhone($phone): SnipcartAddress
    {
        if ($this->_billingAddress === null)
        {
            return $this->_billingAddress = new SnipcartAddress(['phone' => $phone]);
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
     * @param $address
     * @return SnipcartAddress
     */
    public function setShippingAddress($address): SnipcartAddress
    {
        $address = new SnipcartAddress($address);

        return $this->_shippingAddress = $address;
    }

    /**
     * @return string
     */
    public function getShippingAddressName(): string
    {
        return $this->getShippingAddress()->name;
    }

    /**
     * @param $name
     * @return SnipcartAddress
     */
    public function setShippingAddressName($name): SnipcartAddress
    {
        if ($this->_shippingAddress === null)
        {
            return $this->_shippingAddress = new SnipcartAddress(['name' => $name]);
        }

        return $this->_shippingAddress->name = $name;
    }

    /**
     * @return string
     */
    public function getShippingAddressFirstName(): string
    {
        return $this->getShippingAddress()->firstName;
    }

    /**
     * @param $firstName
     * @return SnipcartAddress
     */
    public function setShippingAddressFirstName($firstName): SnipcartAddress
    {
        if ($this->_shippingAddress === null)
        {
            return $this->_shippingAddress = new SnipcartAddress(['firstName' => $firstName]);
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
     * @return SnipcartAddress
     */
    public function setShippingAddressCompanyName($companyName): SnipcartAddress
    {
        if ($this->_shippingAddress === null)
        {
            return $this->_shippingAddress = new SnipcartAddress(['companyName' => $companyName]);
        }

        return $this->_shippingAddress->companyName = $companyName;
    }

    /**
     * @return string
     */
    public function getShippingAddressAddress1(): string
    {
        return $this->getShippingAddress()->address1;
    }

    /**
     * @param $address1
     * @return SnipcartAddress
     */
    public function setShippingAddressAddress1($address1): SnipcartAddress
    {
        if ($this->_shippingAddress === null)
        {
            return $this->_shippingAddress = new SnipcartAddress(['address1' => $address1]);
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
     * @return SnipcartAddress
     */
    public function setShippingAddressAddress2($address2): SnipcartAddress
    {
        if ($this->_shippingAddress === null)
        {
            return $this->_shippingAddress = new SnipcartAddress(['address2' => $address2]);
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
     * @return SnipcartAddress
     */
    public function setShippingAddressCity($city): SnipcartAddress
    {
        if ($this->_shippingAddress === null)
        {
            return $this->_shippingAddress = new SnipcartAddress(['city' => $city]);
        }

        return $this->_shippingAddress->city = $city;
    }

    /**
     * @return string
     */
    public function getShippingAddressCountry(): string
    {
        return $this->getShippingAddress()->country;
    }

    /**
     * @param $country
     * @return SnipcartAddress
     */
    public function setShippingAddressCountry($country): SnipcartAddress
    {
        if ($this->_shippingAddress === null)
        {
            return $this->_shippingAddress = new SnipcartAddress(['country' => $country]);
        }

        return $this->_shippingAddress->country = $country;
    }

    /**
     * @return string
     */
    public function getShippingAddressProvince(): string
    {
        return $this->getShippingAddress()->province;
    }

    /**
     * @param $province
     * @return SnipcartAddress
     */
    public function setShippingAddressProvince($province): SnipcartAddress
    {
        if ($this->_shippingAddress === null)
        {
            return $this->_shippingAddress = new SnipcartAddress(['province' => $province]);
        }

        return $this->_shippingAddress->province = $province;
    }

    /**
     * @return string
     */
    public function getShippingAddressPostalCode(): string
    {
        return $this->getShippingAddress()->postalCode;
    }

    /**
     * @param $postalCode
     * @return SnipcartAddress
     */
    public function setShippingAddressPostalCode($postalCode): SnipcartAddress
    {
        if ($this->_shippingAddress === null)
        {
            return $this->_shippingAddress = new SnipcartAddress(['postalCode' => $postalCode]);
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
     * @return SnipcartAddress
     */
    public function setShippingAddressPhone($phone): SnipcartAddress
    {
        if ($this->_shippingAddress === null)
        {
            return $this->_shippingAddress = new SnipcartAddress(['phone' => $phone]);
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
