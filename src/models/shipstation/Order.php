<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\models\shipstation;

use craft\base\Model;
use fostercommerce\snipcart\models\snipcart\Order as SnipcartOrder;

/**
 * ShipStation Order Model
 * https://www.shipstation.com/developer-api/#/reference/model-order
 *
 * @property Address|null $billTo
 * @property Address|null $shipTo
 * @property OrderItem[] $items
 * @property Weight $weight
 * @property Dimensions $dimensions
 * @property InsuranceOptions $insuranceOptions
 * @property InternationalOptions $internationalOptions
 * @property AdvancedOptions $advancedOptions
 */
class Order extends Model
{
    /**
     * Used for creating a new Order with the ShipStation API.
     */
    public const SCENARIO_NEW = 'new';

    public const STATUS_AWAITING_PAYMENT = 'awaiting_payment';

    public const STATUS_AWAITING_SHIPMENT = 'awaiting_shipment';

    public const STATUS_ON_HOLD = 'on_hold';

    public const STATUS_CANCELLED = 'cancelled';

    /**
     * @var int|null Order ID (read-only)
     */
    public $orderId;

    /**
     * @var string|null Order Number
     */
    public $orderNumber;

    /**
     * @var string|null Order Key
     */
    public $orderKey;

    /**
     * @var \DateTime|null Order Date ("2015-06-29T08:46:27.0000000")
     */
    public $orderDate;

    /**
     * @var \DateTime|null Record Creation Date ("2015-06-29T08:46:27.0000000", read-only)
     */
    public $createDate;

    /**
     * @var \DateTime|null Record Last Modified Date ("2015-06-29T08:46:27.0000000", read-only)
     */
    public $modifyDate;

    /**
     * @var \DateTime|null Order Payment Date ("2015-06-29T08:46:27.0000000")
     */
    public $paymentDate;

    /**
     * @var \DateTime|null Order Ship By Date ("2015-06-29T08:46:27.0000000")
     */
    public $shipByDate;

    /**
     * @var string|null Order Status
     */
    public $orderStatus;

    /**
     * @var int|null Customer ID (read-only)
     */
    public $customerId;

    /**
     * @var string|null Customer Username
     */
    public $customerUsername;

    /**
     * @var string|null Customer Email
     */
    public $customerEmail;

    /**
     * @var float|null Order Total (read-only)
     */
    public $orderTotal;

    /**
     * @var float|null Total amount paid for the order.
     */
    public $amountPaid;

    /**
     * @var float|null Tax amount for the order.
     */
    public $taxAmount;

    /**
     * @var float|null Shipping amount paid by customer, if any.
     */
    public $shippingAmount;

    /**
     * @var string|null Notes left by the customer when placing the order.
     */
    public $customerNotes;

    /**
     * @var string|null Private notes that are only visible to the seller.
     */
    public $internalNotes;

    /**
     * @var bool|null Specifies whether or not this Order is a gift
     */
    public $gift;

    /**
     * @var string|null Gift message left by the customer when placing the order.
     */
    public $giftMessage;

    /**
     * @var string|null Method of payment used by the customer.
     */
    public $paymentMethod;

    /**
     * @var string|null Identifies the shipping service selected by the customer when placing this order. This value is given to ShipStation by the marketplace/cart. If value is "null" then the marketplace or cart does not support this field in ShipStation.
     */
    public $requestedShippingService;

    /**
     * @var string|null The code for the carrier that is to be used(or was used) when this order is shipped (was shipped).
     */
    public $carrierCode;

    /**
     * @var string|null The code for the shipping service that is to be used (or was used) when this order is shipped (was shipped).
     */
    public $serviceCode;

    /**
     * @var string|null The code for the package type that is to be used(or was used) when this order is shipped (was shipped).
     */
    public $packageCode;

    /**
     * @var string|null The type of delivery confirmation that is to be used(or was used) when this order is shipped (was shipped).
     */
    public $confirmation;

    /**
     * @var \DateTime|null The date the order was shipped. ("2015-06-29T08:46:27.0000000")
     */
    public $shipDate;

    /**
     * @var \DateTime|null If placed on hold, this date is the expiration date for this order's hold status. The order is moved back to awaiting_shipment on this date. ("2015-06-29T08:46:27.0000000")
     */
    public $holdUntilDate;

    /**
     * @var int[]|null
     */
    public $tagIds;

    /**
     * @var string|null User assigned to order/shipment (read-only)
     */
    public $userId;

    /**
     * @var bool|null Externally fulfilled (read-only)
     */
    public $externallyFulfilled;

    /**
     * @var string|null Externally fulfilled by (read-only)
     */
    public $externallyFulfilledBy;

    /**
     * @var
     */
    public $labelMessages;

    private ?Address $_billTo = null;

    private ?Address $_shipTo = null;

    /**
     * @var OrderItem[]|null
     */
    private ?array $_items = null;

    private ?Weight $_weight = null;

    private ?Dimensions $_dimensions = null;

    private ?InsuranceOptions $_insuranceOptions = null;

    private ?InternationalOptions $_internationalOptions = null;

    private ?AdvancedOptions $_advancedOptions = null;

    /**
     * Gets the order’s billing address.
     *
     * @return Address|null
     */
    public function getBillTo(): ?Address
    {
        return $this->_billTo;
    }

    /**
     * Sets the order’s billing address.
     *
     * @param Address|array $address The order's billing address.
     */
    public function setBillTo($address): Address
    {
        if (! $address instanceof Address) {
            $address = new Address($address);
        }

        return $this->_billTo = $address;
    }

    /**
     * Gets the order’s shipping address.
     *
     * @return Address|null
     */
    public function getShipTo(): ?Address
    {
        return $this->_shipTo;
    }

    /**
     * Sets the order’s shipping address.
     *
     * @param Address|array $address The order's shipping address.
     */
    public function setShipTo($address): Address
    {
        if (! $address instanceof Address) {
            $address = new Address($address);
        }

        return $this->_shipTo = $address;
    }

    /**
     * Gets the order’s items.
     *
     * @return OrderItem[]
     */
    public function getItems(): array
    {
        if ($this->_items !== null) {
            return $this->_items;
        }

        $this->_items = [];

        return $this->_items;
    }

    /**
     * Sets the order’s items.
     *
     * @param OrderItem[] $items The order's items.
     */
    public function setItems(array $items): void
    {
        $this->_items = $items;
    }

    /**
     * Gets the order’s weight.
     *
     * @return Weight|null
     */
    public function getWeight(): ?Weight
    {
        return $this->_weight;
    }

    /**
     * Sets the order’s weight.
     *
     * @param Weight|array|null $weight The item’s weight.
     *
     * @return Weight|null
     */
    public function setWeight($weight)
    {
        if (! $weight instanceof Weight) {
            $weight = new Weight($weight);
        }

        return $this->_weight = $weight;
    }

    /**
     * Gets the order’s dimensions.
     *
     * @return Dimensions|null
     */
    public function getDimensions(): ?Dimensions
    {
        return $this->_dimensions;
    }

    /**
     * Sets the order’s dimensions.
     *
     * @param Dimensions|array|null $dimensions The order’s dimensions.
     *
     * @return Dimensions|null
     */
    public function setDimensions($dimensions)
    {
        if (! $dimensions instanceof Dimensions) {
            $dimensions = new Dimensions($dimensions);
        }

        return $this->_dimensions = $dimensions;
    }

    /**
     * Gets the order’s insurance options.
     *
     * @return InsuranceOptions|null
     */
    public function getInsuranceOptions(): ?InsuranceOptions
    {
        return $this->_insuranceOptions;
    }

    /**
     * Sets the order’s insurance options.
     *
     * @param InsuranceOptions|array|null $insuranceOptions The order’s insurance options.
     *
     * @return InsuranceOptions|null
     */
    public function setInsuranceOptions($insuranceOptions)
    {
        if (! $insuranceOptions instanceof InsuranceOptions) {
            $insuranceOptions = new InsuranceOptions($insuranceOptions);
        }

        return $this->_insuranceOptions = $insuranceOptions;
    }

    /**
     * Gets the order’s international options.
     *
     * @return InternationalOptions|null
     */
    public function getInternationalOptions(): ?InternationalOptions
    {
        return $this->_internationalOptions;
    }

    /**
     * Sets the order’s international options.
     *
     * @param InternationalOptions|array|null $internationalOptions The order’s international options.
     *
     * @return InternationalOptions|null
     */
    public function setInternationalOptions($internationalOptions)
    {
        if (! $internationalOptions instanceof InternationalOptions) {
            $internationalOptions = new InternationalOptions($internationalOptions);
        }

        return $this->_internationalOptions = $internationalOptions;
    }

    /**
     * Gets the order’s advanced options.
     *
     * @return AdvancedOptions|null
     */
    public function getAdvancedOptions(): ?AdvancedOptions
    {
        return $this->_advancedOptions;
    }

    /**
     * Sets the order’s advanced options.
     *
     * @param AdvancedOptions|array|null $advancedOptions The order’s advanced options.
     *
     * @return AdvancedOptions|null
     */
    public function setAdvancedOptions($advancedOptions)
    {
        if (! $advancedOptions instanceof AdvancedOptions) {
            $advancedOptions = new AdvancedOptions($advancedOptions);
        }

        return $this->_advancedOptions = $advancedOptions;
    }

    /**
     * Map Order properties to this model.
     */
    public static function populateFromSnipcartOrder(SnipcartOrder $snipcartOrder): self
    {
        $items = [];

        foreach ($snipcartOrder->items as $item) {
            $items[] = OrderItem::populateFromSnipcartItem($item);
        }

        return new self([
            'orderNumber' => $snipcartOrder->invoiceNumber,
            'orderKey' => $snipcartOrder->token,
            'orderDate' => $snipcartOrder->creationDate,
            'paymentDate' => $snipcartOrder->completionDate,
            'customerEmail' => $snipcartOrder->email,
            'amountPaid' => $snipcartOrder->total,
            'shippingAmount' => $snipcartOrder->shippingFees,
            'requestedShippingService' => $snipcartOrder->shippingMethod,
            'taxAmount' => $snipcartOrder->taxesTotal,
            'shipTo' => Address::populateFromSnipcartAddress(
                $snipcartOrder->shippingAddress
            ),
            'billTo' => Address::populateFromSnipcartAddress(
                $snipcartOrder->billingAddress
            ),
            'items' => $items,
        ]);
    }

    public function datetimeAttributes(): array
    {
        return [
            'orderDate',
            'createDate',
            'modifyDate',
            'paymentDate',
            'shipByDate',
            'shipDate',
            'holdUntilDate',
        ];
    }

    public function extraFields(): array
    {
        return [
            'billTo',
            'shipTo',
            'items',
            'weight',
            'dimensions',
            'insuranceOptions',
            'internationalOptions',
            'advancedOptions',
        ];
    }

    public function rules(): array
    {
        /**
         * ShipStation uses the ISO 8601 combined format for dateTime stamps
         * being submitted to and returned from the API.
         * `2016-11-29 23:59:59`
         * The time zone represented in all API responses is PST/PDT.
         * Similarly, ShipStation asks that you make all time zone conversions
         * and submit any dateTime requests in PST/PDT.
         */
        return [
            [['orderId', 'customerId', 'userId'],
                'number',
                'integerOnly' => true,
            ],
            [['orderTotal', 'amountPaid', 'taxAmount', 'shippingAmount'],
                'number',
                'integerOnly' => false,
            ],
            [['orderTotal', 'amountPaid', 'taxAmount', 'shippingAmount'],
                'default',
                'value' => 0,
            ],
            [['orderNumber', 'orderStatus'], 'string'],
            [['orderKey', 'customerUsername', 'customerEmail', 'customerNotes', 'internalNotes', 'giftMessage', 'paymentMethod', 'requestedShippingService', 'carrierCode', 'serviceCode', 'packageCode', 'confirmation', 'externallyFulfilledBy'], 'string'],
            [['customerEmail'], 'email'],
            [['gift', 'externallyFulfilled'], 'boolean'],
            [['gift', 'externallyFulfilled'],
                'default',
                'value' => false,
            ],
            [
                'tagIds',
                'each',
                'rule' => ['integer'],
            ],
        ];
    }

    public function scenarios(): array
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_NEW] = ['username'];
        return $scenarios;
    }

    public function getPayloadForPost(): array
    {
        $payload = $this->toArray(
            [],
            $this->extraFields(),
            true
        );

        // TODO: turn this into a proper scenario

        $removeIfNull = [
            'shipByDate',
            'customerId',
            'customerUsername',
            'internalNotes',
            'giftMessage',
            'paymentMethod',
            'packageCode',
            'confirmation',
            'shipDate',
            'holdUntilDate',
            'tagIds',
            'userId',
            'externallyFulfilledBy',
            'labelMessages',
            'insuranceOptions',
            'internationalOptions',
            'advancedOptions',
        ];

        foreach ($removeIfNull as $removeKey) {
            if ($payload[$removeKey] === null) {
                unset($payload[$removeKey]);
            }
        }

        $remove = [
            'orderId',
            'createDate',
            'modifyDate',
            'externallyFulfilled',
            'orderTotal', // read-only field
        ];

        foreach ($remove as $removeKey) {
            unset($payload[$removeKey]);
        }

        $removeFromItems = [
            'orderItemId',
            'adjustment',
            'createDate',
            'modifyDate',
        ];

        foreach ($payload['items'] as &$item) {
            foreach ($removeFromItems as $removeFromItem) {
                unset($item[$removeFromItem]);
            }

            unset($item['weight']['WeightUnits']);
        }

        unset($payload['weight']['WeightUnits']);

        return $payload;
    }
}
