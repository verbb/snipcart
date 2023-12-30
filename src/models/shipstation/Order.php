<?php
namespace verbb\snipcart\models\shipstation;

use verbb\snipcart\models\snipcart\Order as SnipcartOrder;

use craft\base\Model;

use DateTime;

class Order extends Model
{
    // Static Methods
    // =========================================================================

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


    // Constants
    // =========================================================================

    public const SCENARIO_NEW = 'new';
    public const STATUS_AWAITING_PAYMENT = 'awaiting_payment';
    public const STATUS_AWAITING_SHIPMENT = 'awaiting_shipment';
    public const STATUS_ON_HOLD = 'on_hold';
    public const STATUS_CANCELLED = 'cancelled';


    // Properties
    // =========================================================================

    public ?int $orderId = null;
    public ?string $orderNumber = null;
    public ?string $orderKey = null;
    public ?DateTime $orderDate = null;
    public ?DateTime $createDate = null;
    public ?DateTime $modifyDate = null;
    public ?DateTime $paymentDate = null;
    public ?DateTime $shipByDate = null;
    public ?string $orderStatus = null;
    public ?int $customerId = null;
    public ?string $customerUsername = null;
    public ?string $customerEmail = null;
    public ?float $orderTotal = 0;
    public ?float $amountPaid = 0;
    public ?float $taxAmount = 0;
    public ?float $shippingAmount = 0;
    public ?string $customerNotes = null;
    public ?string $internalNotes = null;
    public ?bool $gift = false;
    public ?string $giftMessage = null;
    public ?string $paymentMethod = null;
    public ?string $requestedShippingService = null;
    public ?string $carrierCode = null;
    public ?string $serviceCode = null;
    public ?string $packageCode = null;
    public ?string $confirmation = null;
    public ?DateTime $shipDate = null;
    public ?DateTime $holdUntilDate = null;
    public array $tagIds = [];
    public ?string $userId = null;
    public ?bool $externallyFulfilled = false;
    public ?string $externallyFulfilledBy = null;
    public array $labelMessages = [];

    private ?Address $_billTo = null;
    private ?Address $_shipTo = null;
    private ?array $_items = null;
    private ?Weight $_weight = null;
    private ?Dimensions $_dimensions = null;
    private ?InsuranceOptions $_insuranceOptions = null;
    private ?InternationalOptions $_internationalOptions = null;
    private ?AdvancedOptions $_advancedOptions = null;
    

    // Public Methods
    // =========================================================================

    public function getBillTo(): ?Address
    {
        return $this->_billTo;
    }

    public function setBillTo(?Address $address): Address
    {
        if (!$address instanceof Address) {
            $address = new Address($address);
        }

        return $this->_billTo = $address;
    }

    public function getShipTo(): ?Address
    {
        return $this->_shipTo;
    }

    public function setShipTo(?Address $address): Address
    {
        if (!$address instanceof Address) {
            $address = new Address($address);
        }

        return $this->_shipTo = $address;
    }

    public function getItems(): array
    {
        if ($this->_items !== null) {
            return $this->_items;
        }

        $this->_items = [];

        return $this->_items;
    }

    public function setItems(array $items): void
    {
        $this->_items = $items;
    }

    public function getWeight(): ?Weight
    {
        return $this->_weight;
    }

    public function setWeight(Weight|array|null $weight): ?Weight
    {
        if (!$weight instanceof Weight) {
            $weight = new Weight($weight);
        }

        return $this->_weight = $weight;
    }

    public function getDimensions(): ?Dimensions
    {
        return $this->_dimensions;
    }

    public function setDimensions(Dimensions|array|null $dimensions): ?Dimensions
    {
        if (!$dimensions instanceof Dimensions) {
            $dimensions = new Dimensions($dimensions);
        }

        return $this->_dimensions = $dimensions;
    }

    public function getInsuranceOptions(): ?InsuranceOptions
    {
        return $this->_insuranceOptions;
    }

    public function setInsuranceOptions(InsuranceOptions|array|null $insuranceOptions): ?InsuranceOptions
    {
        if (!$insuranceOptions instanceof InsuranceOptions) {
            $insuranceOptions = new InsuranceOptions($insuranceOptions);
        }

        return $this->_insuranceOptions = $insuranceOptions;
    }

    public function getInternationalOptions(): ?InternationalOptions
    {
        return $this->_internationalOptions;
    }

    public function setInternationalOptions(InternationalOptions|array|null $internationalOptions): ?InternationalOptions
    {
        if (!$internationalOptions instanceof InternationalOptions) {
            $internationalOptions = new InternationalOptions($internationalOptions);
        }

        return $this->_internationalOptions = $internationalOptions;
    }

    public function getAdvancedOptions(): ?AdvancedOptions
    {
        return $this->_advancedOptions;
    }

    public function setAdvancedOptions(AdvancedOptions|array|null $advancedOptions): ?AdvancedOptions
    {
        if (!$advancedOptions instanceof AdvancedOptions) {
            $advancedOptions = new AdvancedOptions($advancedOptions);
        }

        return $this->_advancedOptions = $advancedOptions;
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


    // Protected Methods
    // =========================================================================

    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        
        // ShipStation uses the ISO 8601 combined format for dateTime stamps
        // being submitted to and returned from the API. `2016-11-29 23:59:59`
        // The time zone represented in all API responses is PST/PDT.
        // Similarly, ShipStation asks that you make all time zone conversions
        // and submit any dateTime requests in PST/PDT.
        $rules[] = [['orderId', 'customerId', 'userId'], 'number', 'integerOnly' => true];
        $rules[] = [['orderTotal', 'amountPaid', 'taxAmount', 'shippingAmount'], 'number', 'integerOnly' => false];
        $rules[] = [['customerEmail'], 'email'];
        $rules[] = ['tagIds', 'each', 'rule' => ['integer']];

        return $rules;
    }
}
