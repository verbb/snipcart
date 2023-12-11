<?php
namespace verbb\snipcart\models\snipcart;

use craft\base\Model;
use craft\helpers\UrlHelper;
use DateTime;

class Discount extends Model
{
    // Constants
    // =========================================================================

    public const TRIGGER_CODE = 'Code';
    public const TRIGGER_TOTAL = 'Total';
    public const TRIGGER_PRODUCT = 'Product';
    public const TYPE_FIXED_AMOUNT = 'FixedAmount';
    public const TYPE_FIXED_AMOUNT_ON_ITEMS = 'FixedAmountOnItems';
    public const TYPE_RATE = 'Rate';
    public const TYPE_ALTERNATE_PRICE = 'AlternatePrice';
    public const TYPE_ALTERNATE_SHIPPING = 'Shipping';


    // Properties
    // =========================================================================

    public ?string $id = null;
    public ?string $discountId = null;
    public ?string $name = null;
    public ?DateTime $expires = null;
    public ?int $maxNumberOfUsages = null;
    public ?string $trigger = null;
    public ?string $code = null;
    public ?string $itemId = null;
    public ?float $totalToReach = null;
    public ?string $type = null;
    public ?float $amount = null;
    public ?float $amountSaved = null;
    public ?string $productIds = null;
    public ?float $rate = null;
    public ?string $normalizedRate = null;
    public ?string $alternatePrice = null;
    public ?string $shippingDescription = null;
    public ?float $shippingCost = null;
    public ?int $shippingGuaranteedDaysToDelivery = null;
    public ?int $numberOfUsages = null;
    public ?int $numberOfUsagesUncompleted = null;
    public ?bool $isForARecoveryCampaign = null;
    public ?bool $archived = null;
    public ?string $combinable = null;
    public ?string $maxAmountToReach = null;
    public ?string $maxDiscountsPerItem = null;
    public ?string $appliesOnAllRecurringOrders = null;
    public ?string $quantityOfAProduct = null;
    public ?string $quantityOfProductIds = null;
    public ?string $onlyOnSameProducts = null;
    public ?string $quantityInterval = null;
    public ?string $maxQuantityOfAProduct = null;
    public ?string $numberOfItemsRequired = null;
    public ?string $numberOfFreeItems = null;
    public array $affectedItems = [];
    public ?string $dataAttribute = null;
    public ?string $hasSavedAmount = null;
    public array $products = [];
    public ?string $currency = null;
    public ?DateTime $creationDate = null;
    public ?DateTime $modificationDate = null;
    public array $categories = [];
    public array $categoryNames = [];
    public array $refunds = [];
    public ?string $savedAmount; = null

    private array $_triggerOptionFieldMap = [
        self::TRIGGER_CODE => [
            'code',
        ],
        self::TRIGGER_TOTAL => [
            'totalToReach',
        ],
        self::TRIGGER_PRODUCT => [
            'itemId',
        ],
    ];

    private array $_typeOptionFieldMap = [
        self::TYPE_FIXED_AMOUNT => [
            'amount',
        ],
        self::TYPE_FIXED_AMOUNT_ON_ITEMS => [
            'productIds',
        ],
        self::TYPE_RATE => [
            'rate',
        ],
        self::TYPE_ALTERNATE_PRICE => [
            'alternatePrice',
        ],
        self::TYPE_ALTERNATE_SHIPPING => [
            'shippingDescription',
            'shippingCost',
            'shippingGuaranteedDaysToDelivery',
        ],
    ];


    // Public Methods
    // =========================================================================

    public function rules(): array
    {
        return [
            [['name', 'trigger', 'code', 'itemId', 'type', 'productIds', 'shippingDescription'], 'string'],
            [['name', 'trigger', 'type'], 'required'],
            [['maxNumberOfUsages', 'shippingGuaranteedDaysToDelivery', 'numberOfUsages', 'numberOfUsagesUncompleted'],
                'number',
                'integerOnly' => true,
            ],
            [['totalToReach', 'amount', 'amountSaved', 'rate', 'alternatePrice', 'shippingCost'],
                'number',
                'integerOnly' => false,
            ],
        ];
    }

    public function getPayloadForPost(bool $isNew = true): array
    {
        $remove = [];

        if ($isNew) {
            $remove[] = 'id';
        }

        $payload = $this->toArray();

        // don’t send `false` value as expiration (API rejects it)
        if (isset($payload['expires']) && $payload['expires'] === false) {
            unset($payload['expires']);
        }

        foreach ($remove as $removeKey) {
            unset($payload[$removeKey]);
        }

        foreach ($payload as $key => $value) {
            if ($value === null || $value === '') {
                unset($payload[$key]);
            }
        }

        return $payload;
    }

    public function getTriggerOptionFields(): array
    {
        return $this->_triggerOptionFieldMap[$this->trigger];
    }

    public function getTypeOptionFields(): array
    {
        return $this->_typeOptionFieldMap[$this->type];
    }

    public function getCpUrl(): string
    {
        return UrlHelper::cpUrl('snipcart/discount/' . $this->id);
    }

    public function getDashboardUrl(): string
    {
        return 'https://app.snipcart.com/dashboard/discounts/edit/' . $this->id;
    }
}
