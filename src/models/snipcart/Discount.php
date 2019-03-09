<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models;

/**
 * https://docs.snipcart.com/api-reference/discounts
 */

class Discount extends \craft\base\Model
{
    // Constants
    // =========================================================================

    const TRIGGER_CODE = 'Code';
    const TRIGGER_TOTAL = 'Total';
    const TRIGGER_PRODUCT = 'Product';

    const TYPE_FIXED_AMOUNT = 'FixedAmount';
    const TYPE_FIXED_AMOUNT_ON_ITEMS = 'FixedAmountOnItems';
    const TYPE_RATE = 'Rate';
    const TYPE_ALTERNATE_PRICE = 'AlternatePrice';
    const TYPE_ALTERNATE_SHIPPING = 'Shipping';


    // Public Properties
    // =========================================================================

    /**
     * @var string "2223490d-84c1-480c-b713-50cb0b819313"
     */
    public $id;

    /**
     * @var string The discount friendly name. (required)
     */
    public $name;

    /**
     * @var \DateTime|null The date when this discount should expire, if null, the discount will never expires.
     */
    public $expires;

    /**
     * @var int|null The maximum number of usages for the discount, if null, customers will be able to use this discount indefinitely.
     */
    public $maxNumberOfUsages;

    /**
     * @var string Condition that will trigger the discount. Possible values: `Total`, `Code`, `Product`
     */
    public $trigger;

    /**
     * @var string The code that will need to be entered by the customer. Required when trigger is `Code`
     */
    public $code;

    /**
     * @var string The unique ID of your product defined with `data-item-id`. Required when trigger is `Product`.
     */
    public $itemId;

    /**
     * @var float The minimum order amount. Required when trigger is `Total`.
     */
    public $totalToReach;

    /**
     * @var string The type of action that the discount will apply. (required) Possible values: `FixedAmount`, `FixedAmountOnItems`, `Rate`, `AlternatePrice`, `Shipping`
     */
    public $type;

    /**
     * @var float The amount that will be deducted from order total. Required when type is `FixedAmount`.
     */
    public $amount;

    /**
     * @var string  A comma separated list of unique ID of your products defined with data-item-id.
     *              The fixed amount will be deducted from each product that matches.
     *              Required when type is `FixedAmountOnItems`.
     */
    public $productIds;

    /**
     * @var float The rate in percentage that will be deducted from order total. Required when type is `Rate`.
     */
    public $rate;

    /**
     * @var
     */
    public $normalizedRate;

    /**
     * @var string|null The name of the alternate price list to use. Required when type is `AlternatePrice`.
     */
    public $alternatePrice;

    /**
     * @var string The shipping method name that will be displayed to your customers. Required when type is `Shipping`.
     */
    public $shippingDescription;

    /**
     * @var float The shipping amount that will be available to your customers. Required when type is `Shipping`.
     */
    public $shippingCost;

    /**
     * @var int The number of days it will take for shipping, you can leave it to null.
     */
    public $shippingGuaranteedDaysToDelivery;

    /**
     * @var int
     */
    public $numberOfUsages;

    /**
     * @var int
     */
    public $numberOfUsagesUncompleted;

    /**
     * @var bool
     */
    public $isForARecoveryCampaign;

    /**
     * @var bool
     */
    public $archived;

    /**
     * @var
     */
    public $combinable;

    /**
     * @var
     */
    public $maxAmountToReach;

    /**
     * @var
     */
    public $appliesOnAllRecurringOrders;

    /**
     * @var
     */
    public $quantityOfAProduct;

    /**
     * @var
     */
    public $quantityOfProductIds;

    /**
     * @var
     */
    public $onlyOnSameProducts;

    /**
     * @var
     */
    public $quantityInterval;

    /**
     * @var
     */
    public $maxQuantityOfAProduct;

    /**
     * @var
     */
    public $numberOfItemsRequired;

    /**
     * @var
     */
    public $numberOfFreeItems;

    /**
     * @var
     */
    public $affectedItems;

    /**
     * @var
     */
    public $dataAttribute;

    /**
     * @var
     */
    public $hasSavedAmount;

    /**
     * @var
     */
    public $products;

    /**
     * @var
     */
    public $creationDate;

    /**
     * @var
     */
    public $modificationDate;


    // Private Properties
    // =========================================================================

    private $_triggerOptionFieldMap = [
        self::TRIGGER_CODE => [
            'code'
        ],
        self::TRIGGER_TOTAL => [
            'totalToReach'
        ],
        self::TRIGGER_PRODUCT => [
            'itemId'
        ],
    ];

    private $_typeOptionFieldMap = [
        self::TYPE_FIXED_AMOUNT => [
            'amount'
        ],
        self::TYPE_FIXED_AMOUNT_ON_ITEMS => [
            'productIds'
        ],
        self::TYPE_RATE => [
            'rate'
        ],
        self::TYPE_ALTERNATE_PRICE => [
            'alternatePrice'
        ],
        self::TYPE_ALTERNATE_SHIPPING => [
            'shippingDescription',
            'shippingCost',
            'shippingGuaranteedDaysToDelivery',
        ],
    ];


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function datetimeAttributes(): array
    {
        return ['expires'];
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['name', 'trigger', 'code', 'itemId', 'type', 'productIds', 'shippingDescription'], 'string'],
            [['name', 'trigger', 'type'], 'required'],
            [['maxNumberOfUsages', 'shippingGuaranteedDaysToDelivery', 'numberOfUsages', 'numberOfUsagesUncompleted'], 'number', 'integerOnly' => true],
            [['totalToReach', 'amount', 'rate', 'alternatePrice', 'shippingCost'], 'number', 'integerOnly' => false],
        ];
    }

    /**
     * Remove cruft for posting to the REST API. This should be in a scenario
     * once it's clear how to get them working.
     *
     * @return array
     */
    public function getPayloadForPost(): array
    {
        $remove = ['id'];
        $payload = $this->toArray();

        // don't send `false` value as expiration (API rejects it)
        if (isset($payload['expires']) && $payload['expires'] === false)
        {
            unset($payload['expires']);
        }

        foreach ($remove as $removeKey)
        {
            unset($payload[$removeKey]);
        }

        foreach ($payload as $key => $value)
        {
            if ($value === null || $value === '')
            {
                unset($payload[$key]);
            }
        }

        return $payload;
    }

    /**
     * Get a list of field options relevant to the selected trigger.
     *
     * @return array
     */
    public function getTriggerOptionFields(): array
    {
        return $this->_triggerOptionFieldMap[$this->trigger];
    }

    /**
     * Get a list of discount field options relevant to the selected type.
     *
     * @return array
     */
    public function getTypeOptionFields(): array
    {
        return $this->_typeOptionFieldMap[$this->type];
    }

    /**
     * Returns the Craft control panel URL for the detail page.
     * @return string
     */
    public function getCpUrl(): string
    {
        return \craft\helpers\UrlHelper::cpUrl('snipcart/discount/' . $this->id);
    }

    public function getDashboardUrl(): string
    {
        return 'https://app.snipcart.com/dashboard/discounts/edit/' . $this->id;
    }

}
