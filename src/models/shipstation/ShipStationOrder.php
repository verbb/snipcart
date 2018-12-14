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
 * ShipStation Order Model
 * https://www.shipstation.com/developer-api/#/reference/model-order
 *
 * @property ShipStationAddress $billTo
 * @property ShipStationAddress $shipTo
 * @property ShipStationOrderItem[] $items
 * @property ShipStationWeight $weight
 * @property ShipStationDimensions $dimensions
 * @property ShipStationInsuranceOptions $insuranceOptions
 * @property ShipStationInternationalOptions $internationalOptions
 * @property ShipStationAdvancedOptions $advancedOptions
 */
class ShipStationOrder extends Model
{
    // Constants
    // =========================================================================

    const STATUS_AWAITING_PAYMENT  = 'awaiting_payment';
    const STATUS_AWAITING_SHIPMENT = 'awaiting_shipment';
    const STATUS_ON_HOLD           = 'on_hold';
    const STATUS_CANCELLED         = 'cancelled';


    // Properties
    // =========================================================================

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
     * @var string|null Order Date ("2015-06-29T08:46:27.0000000")
     */
    public $orderDate;

    /**
     * @var string|null Record Creation Date ("2015-06-29T08:46:27.0000000", read-only)
     */
    public $createDate;

    /**
     * @var string|null Record Last Modified Date ("2015-06-29T08:46:27.0000000", read-only)
     */
    public $modifyDate;

    /**
     * @var string|null Order Payment Date ("2015-06-29T08:46:27.0000000")
     */
    public $paymentDate;

    /**
     * @var string|null Order Ship By Date ("2015-06-29T08:46:27.0000000")
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
     * @var ShipStationAddress|null
     */
    private $_billTo;

    /**
     * @var ShipStationAddress|null
     */
    private $_shipTo;

    /**
     * @var ShipStationOrderItem[]|null
     */
    private $_items;

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
     * @var string|null The date the order was shipped. ("2015-06-29T08:46:27.0000000")
     */
    public $shipDate;
    
    /**
     * @var string|null If placed on hold, this date is the expiration date for this order's hold status. The order is moved back to awaiting_shipment on this date. ("2015-06-29T08:46:27.0000000")
     */
    public $holdUntilDate;

    /**
     * @var ShipStationWeight|null
     */
    private $_weight;

    /**
     * @var ShipStationDimensions|null
     */
    private $_dimensions;

    /**
     * @var ShipStationInsuranceOptions|null
     */
    private $_insuranceOptions;

    /**
     * @var ShipStationInternationalOptions|null
     */
    private $_internationalOptions;

    /**
     * @var ShipStationAdvancedOptions|null
     */
    private $_advancedOptions;

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


    // Public Methods
    // =========================================================================

    /**
     * Gets the order’s billing address.
     *
     * @return ShipStationAddress
     */
    public function getBillTo(): ShipStationAddress
    {
        return $this->_billTo;
    }


    /**
     * Sets the order’s billing address.
     *
     * @param ShipStationAddress $address The order's billing address.
     *
     * @return ShipStationAddress
     */
    public function setBillTo(ShipStationAddress $address): ShipStationAddress
    {
        return $this->_billTo = $address;
    }


    /**
     * Gets the order’s shipping address.
     *
     * @return ShipStationAddress
     */
    public function getShipTo(): ShipStationAddress
    {
        return $this->_shipTo;
    }


    /**
     * Sets the order’s shipping address.
     *
     * @param ShipStationAddress $address The order's shipping address.
     *
     * @return ShipStationAddress
     */
    public function setShipTo(ShipStationAddress $address): ShipStationAddress
    {
        return $this->_shipTo = $address;
    }


    /**
     * Gets the order’s items.
     *
     * @return ShipStationOrderItem[]
     */
    public function getItems(): array
    {
        if ($this->_items !== null)
        {
            return $this->_items;
        }

        $this->_items = [];

        return $this->_items;
    }


    /**
     * Sets the order’s items.
     *
     * @param ShipStationOrderItem[] $items The order's items.
     *
     * @return void
     */
    public function setItems(array $items)
    {
        $this->_items = $items;
    }


    /**
     * Gets the order’s weight.
     *
     * @return ShipStationWeight
     */
    public function getWeight()
    {
        return $this->_weight;
    }


    /**
     * Sets the order’s weight.
     *
     * @param array|ShipStationWeight $weight The item’s weight.
     *
     * @return ShipStationWeight
     */
    public function setWeight($weight): ShipStationWeight
    {
        if (is_array($weight))
        {
            $weight = new ShipStationWeight($weight);
        }

        return $this->_weight = $weight;
    }


    /**
     * Gets the order’s dimensions.
     *
     * @return ShipStationDimensions
     */
    public function getDimensions()
    {
        return $this->_dimensions;
    }


    /**
     * Sets the order’s dimensions.
     *
     * @param ShipStationDimensions $dimensions The order’s dimensions.
     *
     * @return ShipStationDimensions
     */
    public function setDimensions(ShipStationDimensions $dimensions): ShipStationDimensions
    {
        return $this->_dimensions = $dimensions;
    }


    /**
     * Gets the order’s insurance options.
     *
     * @return ShipStationInsuranceOptions
     */
    public function getInsuranceOptions()
    {
        return $this->_insuranceOptions;
    }


    /**
     * Sets the order’s insurance options.
     *
     * @param ShipStationInsuranceOptions $insuranceOptions The order’s insurance options.
     *
     * @return ShipStationInsuranceOptions
     */
    public function setInsuranceOptions(ShipStationInsuranceOptions $insuranceOptions): ShipStationInsuranceOptions
    {
        return $this->_insuranceOptions = $insuranceOptions;
    }


    /**
     * Gets the order’s international options.
     *
     * @return ShipStationInternationalOptions
     */
    public function getInternationalOptions()
    {
        return $this->_internationalOptions;
    }


    /**
     * Sets the order’s international options.
     *
     * @param ShipStationInternationalOptions $internationalOptions The order’s international options.
     *
     * @return ShipStationInternationalOptions
     */
    public function setInternationalOptions(ShipStationInternationalOptions $internationalOptions): ShipStationInternationalOptions
    {
        return $this->_internationalOptions = $internationalOptions;
    }


    /**
     * Gets the order’s advanced options.
     *
     * @return ShipStationAdvancedOptions
     */
    public function getAdvancedOptions()
    {
        return $this->_advancedOptions;
    }


    /**
     * Sets the order’s advanced options.
     *
     * @param ShipStationAdvancedOptions $advancedOptions The order’s advanced options.
     *
     * @return ShipStationAdvancedOptions
     */
    public function setAdvancedOptions(ShipStationAdvancedOptions $advancedOptions): ShipStationAdvancedOptions
    {
        return $this->_advancedOptions = $advancedOptions;
    }


    /**
     * @inheritdoc
     */
    public function extraFields()
    {
        return ['billTo', 'shipTo', 'items', 'weight', 'dimensions', 'insuranceOptions', 'internationalOptions', 'advancedOptions'];
    }


    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        /**
         * TODO: validate and transform dates properly
         *
         * ShipStation uses the ISO 8601 combined format for dateTime stamps being submitted to and returned from the API.
         * `2016-11-29 23:59:59`
         * The time zone represented in all API responses is PST/PDT. Similarly, ShipStation asks that you make all time zone conversions and submit any dateTime requests in PST/PDT.
         */
        return [
            [['orderId', 'customerId', 'userId'], 'number', 'integerOnly' => true],
            [['orderTotal', 'amountPaid', 'taxAmount', 'shippingAmount'], 'number', 'integerOnly' => false],
            [['orderTotal', 'amountPaid', 'taxAmount', 'shippingAmount'], 'default', 'value' => 0],
            [['orderNumber', 'orderStatus', 'orderDate', 'createDate', 'modifyDate', 'paymentDate', 'shipByDate', 'shipDate', 'holdUntilDate'], 'string'],
            [['orderKey', 'customerUsername', 'customerEmail', 'customerNotes', 'internalNotes', 'giftMessage', 'paymentMethod', 'requestedShippingService', 'carrierCode', 'serviceCode', 'packageCode', 'confirmation', 'externallyFulfilledBy'], 'string'],
            [['customerEmail'], 'email'],
            [['gift', 'externallyFulfilled'], 'boolean'],
            [['gift', 'externallyFulfilled'], 'default', 'value' => false],
            ['tagIds', 'each', 'rule' => ['integer']],
        ];
    }

}
