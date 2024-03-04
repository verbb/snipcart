<?php
namespace verbb\snipcart\models\snipcart;

use verbb\snipcart\behaviors\BillingAddressBehavior;
use verbb\snipcart\behaviors\ShippingAddressBehavior;
use verbb\snipcart\helpers\ModelHelper;

use Craft;
use craft\base\Model;
use craft\helpers\UrlHelper;

use DateTime;
use stdClass;

class Order extends Model
{
    // Constants
    // =========================================================================

    public const PAYMENT_METHOD_CREDIT_CARD = 'CreditCard';
    public const PAYMENT_STATUS_PAID = 'Paid';
    public const STATUS_IN_PROGRESS = 'InProgress';
    public const STATUS_PROCESSED = 'Processed';
    public const STATUS_DISPUTED = 'Disputed';
    public const STATUS_SHIPPPED = 'Shipped';
    public const STATUS_DELIVERED = 'Delivered';
    public const STATUS_PENDING = 'Pending';
    public const STATUS_CANCELLED = 'Cancelled';


    // Properties
    // =========================================================================

    public ?string $token = null;
    public ?string $parentToken = null;
    public ?DateTime $creationDate = null;
    public ?DateTime $modificationDate = null;
    public ?DateTime $completionDate = null;
    public ?string $status = null;
    public ?string $paymentStatus = null;
    public ?string $paymentMethod = null;
    public ?string $invoiceNumber = null;
    public ?string $parentInvoiceNumber = null;
    public ?string $email = null;
    public ?string $cardHolderName = null;
    public ?string $creditCardLast4Digits = null;
    public ?string $notes = null;
    public ?bool $shippingAddressSameAsBilling = null;
    public ?bool $isRecurringOrder = null;
    public ?float $finalGrandTotal = null;
    public ?float $shippingFees = null;
    public ?string $shippingMethod = null;
    public array $taxes = [];
    public ?string $promocodes = null;
    public ?bool $willBePaidLater = null;
    public ?array $customFields = null;
    public ?string $paymentTransactionId = null;
    public ?string $subscriptionId = null;
    public ?string $paymentGatewayUsed = null;
    public ?string $taxProvider = null;
    public ?string $lang = null;
    public ?bool $billingAddressComplete = null;
    public ?bool $shippingAddressComplete = null;
    public ?bool $shippingMethodComplete = null;
    public ?string $rebateAmount = null;
    public ?string $currency = null;
    public ?string $recoveredFromCampaignId = null;
    public ?string $trackingNumber = null;
    public ?string $trackingUrl = null;
    public ?string $shippingProvider = null;
    public ?string $customFieldsJson = null;
    public ?string $userId = null;
    public ?string $cardType = null;
    public ?float $refundsAmount = null;
    public ?float $adjustedAmount = null;
    public ?float $savedAmount = null;
    public ?int $totalNumberOfItems = null;
    public ?float $subtotal = null;
    public ?float $baseTotal = null;
    public ?float $itemsTotal = null;
    public ?float $taxableTotal = null;
    public ?float $grandTotal = null;
    public ?string $total = null;
    public ?string $totalWeight = null;
    public ?string $totalRebateRate = null;
    public ?string $shippingEnabled = null;
    public ?string $numberOfItemsInOrder = null;
    public stdClass|array|null $metadata = null;
    public ?string $taxesTotal = null;
    public ?string $itemsCount = null;
    public stdClass|array|null $summary = null;
    public ?string $ipAddress = null;
    public ?string $userAgent = null;
    public ?bool $hasSubscriptions = null;
    public ?string $compatibilitySwitches = null;
    public ?string $totalPriceWithoutDiscountsAndTaxes = null;
    public stdClass|array|null $paymentDetails = null;

    private mixed $_user = null;
    private ?Address $_billingAddress = null;
    private ?Address $_shippingAddress = null;
    private array $_discounts = [];
    private array $_plans = [];
    private array $_items = [];
    private array $_refunds = [];


    // Public Methods
    // =========================================================================

    public function getDiscounts(): array
    {
        return $this->_discounts;
    }

    public function setDiscounts(array $discounts): mixed
    {
        return $this->_discounts = $discounts;
    }

    public function getItems(): array
    {
        return $this->_items;
    }

    public function setItems(array $items): ?array
    {
        foreach ($items as &$item) {
            if (!$item instanceof Item) {
                $itemData = ModelHelper::stripUnknownProperties($item, Item::class);

                $item = new Item((array)$itemData);
            }
        }

        return $this->_items = $items;
    }

    public function getPlans(): array
    {
        return $this->_plans;
    }

    public function setPlans(array $plans): mixed
    {
        return $this->_plans = $plans;
    }

    public function getRefunds(): array
    {
        return $this->_refunds;
    }

    public function setRefunds(array $refunds): mixed
    {
        return $this->_refunds = $refunds;
    }

    public function getUser(): Customer
    {
        return $this->_user;
    }

    public function setUser(mixed $user): mixed
    {
        if ($user === null) {
            return null;
        }

        if (gettype($user) === 'object') {
            $user = (array)$user;
        }

        // added a bit to get a user element based on the email passed in from $user as it is an array
        $craftUser = Craft::$app->users->getUserByUsernameOrEmail($user['email']);
        
        return $this->_user = $craftUser;
    }

    public function setBillingAddress(array|stdClass|Address $address): ?Address
    {
        if (!$address instanceof Address) {
            if ($address === null) {
                $address = [];
            }

            $addrData = ModelHelper::stripUnknownProperties($address, Address::class);

            $address = new Address((array)$addrData);
        }

        return $this->_billingAddress = $address;
    }

    public function setShippingAddress(array|stdClass|Address $address): ?Address
    {
        if (!$address instanceof Address) {
            if ($address === null) {
                $address = [];
            }

            $addrData = ModelHelper::stripUnknownProperties($address, Address::class);
            $addressModel = new Address((array)$addrData);
        }

        return $this->_shippingAddress = $addressModel;
    }

    public function getCpUrl(): string
    {
        return UrlHelper::cpUrl('snipcart/order/' . $this->token);
    }

    public function getDashboardUrl(): ?string
    {
        if (!isset($this->token)) {
            return null;
        }

        return 'https://app.snipcart.com/dashboard/orders/' . $this->token;
    }

    public function hasShippableItems(): bool
    {
        foreach ($this->items as $item) {
            if ($item->shippable) {
                return true;
            }
        }

        return false;
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors['billingAddress'] = [
            'class' => BillingAddressBehavior::class,
        ];

        $behaviors['shippingAddress'] = [
            'class' => ShippingAddressBehavior::class,
        ];

        return $behaviors;
    }

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
            'cpUrl',
        ];
    }
    

    // Protected Methods
    // =========================================================================

    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        
        $rules[] = [['finalGrandTotal', 'shippingFees', 'rebateAmount'], 'number', 'integerOnly' => false];

        return $rules;
    }
}
