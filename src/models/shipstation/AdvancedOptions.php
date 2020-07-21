<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models\shipstation;

/**
 * ShipStation Advanced Options Model
 * https://www.shipstation.com/developer-api/#/reference/model-advancedoptions
 */
class AdvancedOptions extends \craft\base\Model
{
    /**
     * @var int|null Specifies the warehouse where to the order is to ship from.
     *               If the order was fulfilled using a fill provider,
     *               no warehouse is attached to these orders and will result
     *               in a null value being returned.
     */
    public $warehouseId;

    /**
     * @var bool|null Specifies whether the order is non-machinable.
     */
    public $nonMachinable;

    /**
     * @var bool|null Specifies whether the order is to be delivered on
     *                a Saturday.
     */
    public $saturdayDelivery;

    /**
     * @var bool|null Specifies whether the order contains alcohol.
     */
    public $containsAlcohol;

    /**
     * @var int|null ID of store that is associated with the order. If not
     *               specified in the CreateOrder call either to create
     *               or update an order, ShipStation will default to the first
     *               manual store on the account.
     */
    public $storeId;

    /**
     * @var string|null Field that allows for custom data
     *                  to be associated with an order.
     */
    public $customField1;

    /**
     * @var string|null Field that allows for custom data
     *                  to be associated with an order.
     */
    public $customField2;

    /**
     * @var string|null Field that allows for custom data
     *                  to be associated with an order.
     */
    public $customField3;

    /**
     * @var string|null Identifies the original source/marketplace of the order.
     */
    public $source;

    /**
     * @var bool Read-Only: Returns whether or not an order has been merged or
     *           split with another order.
     */
    public $mergedOrSplit;

    /**
     * @var int[] Read-Only: Array of orderIds. Each orderId identifies an order
     *            that was merged with the associated order.
     */
    public $mergedIds;

    /**
     * @var int Read-Only: If an order has been split, it will return the
     *          Parent ID of the order with which it has been split.
     *          If the order has not been split, this field will return null.
     */
    public $parentId;

    /**
     * @var string|null Identifies which party to bill. Possible values:
     *                  "my_account", **"my_other_account", "recipient",
     *                  "third_party".
     */
    public $billToParty;

    /**
     * @var string|null Account number of billToParty.
     */
    public $billToAccount;

    /**
     * @var string|null Postal Code of billToParty.
     */
    public $billToPostalCode;

    /**
     * @var string|null Country Code of billToParty.
     */
    public $billToCountryCode;

    /**
     * @var string|null When using my_other_account billToParty value,
     *                  the shippingProviderId value associated with
     *                  the desired account.
     */
    public $billToMyOtherAccount;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['warehouseId', 'storeId', 'parentId'], 'number', 'integerOnly' => true],
            [['customField1', 'customField2', 'customField3', 'source', 'billToParty', 'billToAccount', 'billToPostalCode', 'billToCountryCode', 'billToMyOtherAccount'], 'string'],
            [['nonMachinable', 'saturdayDelivery', 'containsAlcohol', 'mergedOrSplit'], 'boolean'],
            ['mergedIds', 'each', 'rule' => ['integer']],
            [['billToCountryCode'], 'string', 'length' => 2],
        ];
    }

}
