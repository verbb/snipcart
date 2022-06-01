<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\models\snipcart;

use fostercommerce\snipcart\Snipcart;
use fostercommerce\snipcart\behaviors\BillingAddressBehavior;
use fostercommerce\snipcart\behaviors\ShippingAddressBehavior;
use fostercommerce\snipcart\helpers\ModelHelper;

/**
 * Snipcart Order model
 * https://docs.snipcart.com/v2/api-reference/orders
 *
 * @package fostercommerce\snipcart\models\snipcart
 *
 * @property Address $billingAddress
 * @property Address $shippingAddress
 * @property Customer|null $user
 * @property Discount[] $discounts
 * @property Plan[] $plans
 * @property Item[] $items
 * @property string $billingAddressName
 * @property string $billingAddressFirstName
 * @property string $billingAddressCompanyName
 * @property string $billingAddressAddress1
 * @property string $billingAddressAddress2
 * @property string $billingAddressCity
 * @property string $billingAddressCountry
 * @property string $billingAddressProvince
 * @property string $billingAddressPostalCode
 * @property string $billingAddressPhone
 * @property string $shippingAddressName
 * @property string $shippingAddressFirstName
 * @property string $shippingAddressCompanyName
 * @property string $shippingAddressAddress1
 * @property string $shippingAddressAddress2
 * @property string $shippingAddressCity
 * @property string $shippingAddressCountry
 * @property string $shippingAddressProvince
 * @property string $shippingAddressPostalCode
 * @property string $shippingAddressPhone
 * @property string $dashboardUrl
 */
class Order extends \craft\base\Model
{
    const PAYMENT_METHOD_CREDIT_CARD = 'CreditCard';
    const PAYMENT_STATUS_PAID = 'Paid';

    const STATUS_IN_PROGRESS = 'InProgress';
    const STATUS_PROCESSED = 'Processed';
    const STATUS_DISPUTED = 'Disputed';
    const STATUS_SHIPPPED = 'Shipped';
    const STATUS_DELIVERED = 'Delivered';
    const STATUS_PENDING = 'Pending';
    const STATUS_CANCELLED = 'Cancelled';

    /**
     * @var string
     */
    public string $token;

    /**
     * @var string|null
     */
    public string $parentToken;

    /**
     * @var \DateTime Date order was created. ("2018-12-05T18:37:19Z")
     */
    public \DateTime $creationDate;

    /**
     * @var \DateTime Date order was last modified. ("2018-12-05T18:37:19Z")
     */
    public \DateTime $modificationDate;

    /**
     * @var \DateTime Date the order was completed.
     */
    public \DateTime $completionDate;

    /**
     * @var string Order status.
     */
    public string $status;

    /**
     * @var string
     */
    public string $paymentStatus;

    /**
     * @var string
     */
    public string $paymentMethod;

    /**
     * @var string
     */
    public string $invoiceNumber;

    /**
     * @var string
     */
    public string $parentInvoiceNumber;

    /**
     * @var string
     */
    public string $email;

    /**
     * @var Customer|null
     */
    private mixed $_user;

    /**
     * @var string
     */
    public string $cardHolderName;

    /**
     * @var
     */
    public string $creditCardLast4Digits;

    /**
     * @var Address
     */
    private Address $_billingAddress;

    /**
     * @var Address
     */
    private Address $_shippingAddress;

    /**
     * @var string
     */
    public string $notes;

    /**
     * @var bool
     */
    public bool $shippingAddressSameAsBilling;

    /**
     * @var bool
     */
    public bool $isRecurringOrder;

    /**
     * @var float
     */
    public float $finalGrandTotal;

    /**
     * @var float
     */
    public float $shippingFees;

    /**
     * @var string
     */
    public string $shippingMethod;

    /**
     * @var Discount[]
     */
    private $_discounts = [];

    /**
     * @var Plan[]
     */
    private $_plans = [];

    /**
     * @var Item[]
     */
    private $_items = [];

    /**
     * @var Refund[]
     */
    private $_refunds = [];

    /**
     * @var array
     */
    public array $taxes;

    /**
     * @var
     */
    public $promocodes;

    /**
     * @var bool
     */
    public $willBePaidLater;

    /**
     * @var \fostercommerce\snipcart\models\CustomField[]|null
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
     * @var string
     */
    public $trackingNumber;

    /**
     * @var string
     */
    public $trackingUrl;

    /**
     * @var string
     */
    public $shippingProvider;

    /**
     * @var string JSON representation of `$customFields`
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
     * @var float
     */
    public $refundsAmount;

    /**
     * @var float
     */
    public $adjustedAmount;

    /**
     * @var float
     */
    public $savedAmount;

    /**
     * @var int
     */
    public $totalNumberOfItems;

    /**
     * @var float
     */
    public $subtotal;

    /**
     * @var float
     */
    public $baseTotal;

    /**
     * @var float
     */
    public $itemsTotal;

    /**
     * @var float
     */
    public $taxableTotal;

    /**
     * @var float
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
     * @var string
     */
    public $ipAddress;

    /**
     * @var string
     */
    public $userAgent;

    /**
     * @var bool
     */
    public $hasSubscriptions;

    /**
     * @var
     */
    public $compatibilitySwitches;

    /**
     * @var
     */
    public $totalPriceWithoutDiscountsAndTaxes;

    /**
     * @var
     */
    public $paymentDetails;

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
    public function setDiscounts($discounts): mixed
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
     * @param mixed[] $items
     * @return array|null
     */
    public function setItems($items): ?array
    {
        foreach ($items as &$item) {
            if (! $item instanceof Item) {
                $itemData = ModelHelper::stripUnknownProperties(
                    $item,
                    Item::class
                );

                $item = new Item((array) $itemData);
            }
        }

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
    public function setPlans($plans): mixed
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
    public function setRefunds($refunds): mixed
    {
        return $this->_refunds = $refunds;
    }

    /**
     * @return Customer|null
     */
    public function getUser(): Customer
    {
        return $this->_user;
    }

    /**
     * @param $user
     * @return mixed
     */
    public function setUser(array|object $user): mixed
    {   
        if(gettype($user) == 'object'){
            $user = (array) $user;
        }
       // added a bit to get a user element based on the email passed in from $user as it is an array
        $craftUser = \Craft::$app->users->getUserByUsernameOrEmail($user['email']);
        return $this->_user = $craftUser;
    }


    /**
     * @param Address|array $address
     * @return Address
     */
    public function setBillingAddress($address): ?Address
    {
       
        if (! $address instanceof Address) {
            if($address === null){
                $address = [];
            }
            $addrData = ModelHelper::stripUnknownProperties(
                $address,
                Address::class
            );

            $address = new Address((array)$addrData);
        }

        return $this->_billingAddress = $address;
    }

    /**
     * @param Address|array $address
     * @return Address
     */
    public function setShippingAddress($address): ?Address
    {    
        if (! $address instanceof Address) {
            if($address === null){
                $address = [];
            }
            $addrData = ModelHelper::stripUnknownProperties(
                $address,
                Address::class
            );
            $addressModel = new Address((array)$addrData);
        }
    
      
        return $this->_shippingAddress = $addressModel;
    }

    /**
     * Returns the Craft control panel URL for the detail page.
     *
     * @return string
     */
    public function getCpUrl(): string
    {
        return \craft\helpers\UrlHelper::cpUrl('snipcart/order/' . $this->token);
    }

    /**
     * Returns the URL for the order in the Snipcart customer dashboard.
     *
     * @return string|null
     */
    public function getDashboardUrl(): ?string
    {
        if (! isset($this->token)) {
            return null;
        }

        return 'https://app.snipcart.com/dashboard/orders/' . $this->token;
    }

    /**
     * Returns `true` if order items contain at least one OrderItem with
     * a `shippable` property that's `true.
     *
     * @return bool
     */
    public function hasShippableItems(): bool
    {
        foreach ($this->items as $item) {
            if ($item->shippable) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function datetimeAttributes(): array
    {
        return ['creationDate', 'modificationDate', 'completionDate'];
    }

    /**
     * @inheritdoc
     *
     * Proxy all our billingAddress* and shippingAddress* fields without having
     * to use a whole bunch of getters and setters on this model.
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors['billingAddress'] = [
            'class' => BillingAddressBehavior::class
        ];

        $behaviors['shippingAddress'] = [
            'class' => ShippingAddressBehavior::class
        ];

        return $behaviors;
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
            //[['creationDate', 'modificationDate', 'completionDate'], 'date', 'format' => 'php:c'],
            [['finalGrandTotal', 'shippingFees', 'rebateAmount'], 'number', 'integerOnly' => false],
        ];
    }

    /**
     * @inheritdoc
     */
    public function extraFields(): array
    {
        return [
            'billingAddress',
            'shippingAddress',
            'user',
            'discounts',
            'refunds',
            'plans',
            'items',
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
            'shippingAddressFirstName',
            'shippingAddressCompanyName',
            'shippingAddressAddress1',
            'shippingAddressAddress2',
            'shippingAddressCity',
            'shippingAddressCountry',
            'shippingAddressProvince',
            'shippingAddressPostalCode',
            'shippingAddressPhone',
            'cpUrl'
        ];
    }

}
