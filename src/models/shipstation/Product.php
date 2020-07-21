<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models\shipstation;

/**
 * ShipStation Product Model
 * https://www.shipstation.com/developer-api/#/reference/model-product
 */
class Product extends \craft\base\Model
{
    /**
     * @var int|null The system generated identifier for the product.
     *               This is a read-only field.
     */
    public $productId;

    /**
     * @var string|null Stock keeping Unit. A user-defined value for a product
     *                  to help identify the product. It is suggested that each
     *                  product should contain a unique SKU.
     */
    public $sku;

    /**
     * @var string|null Name or description of the product.
     */
    public $name;

    /**
     * @var float|null The unit price of the product.
     */
    public $price;

    /**
     * @var float|null The seller's cost for this product.
     */
    public $defaultCost;

    /**
     * @var int|null The length of the product. Unit of measurement is UI
     *               dependent. No conversions will be made from one UOM
     *               to another. See knowledge base for more details.
     */
    public $length;

    /**
     * @var int|null The width of the product. Unit of measurement is UI
     *               dependent. No conversions will be made from one UOM
     *               to another. See knowledge base for more details.
     */
    public $width;

    /**
     * @var int|null The height of the product. Unit of measurement is UI
     *               dependent. No conversions will be made from one UOM
     *               to another. See knowledge base for more details.
     */
    public $height;

    /**
     * @var int|null The weight of a single item in ounces.
     */
    public $weightOz;

    /**
     * @var string|null Seller's private notes for the product.
     */
    public $internalNotes;

    /**
     * @var string|null Stock keeping Unit for the fulfillment of that product
     *                  by a 3rd party.
     */
    public $fulfillmentSku;

    /**
     * @var \DateTime|null The timestamp the product record was created in
     *                     ShipStation's database. Read-Only.
     */
    public $createDate;

    /**
     * @var \DateTime|null The timestamp the product record was modified
     *                     in ShipStation. Read-Only.
     */
    public $modifyDate;

    /**
     * @var bool|null Specifies whether or not the product is an active record.
     */
    public $active;

    /**
     * @var ProductCategory|null The Product Category used to organize and
     *                           report on similar products. See knowledge base
     *                           for more information on Product Categories.
     */
    public $productCategory;

    /**
     * @var string|null Specifies the product type. See knowledge base for more
     *                  information on Product Types.
     */
    public $productType;

    /**
     * @var string|null The warehouse location associated with the
     *                  product record.
     */
    public $warehouseLocation;

    /**
     * @var string|null The default domestic shipping carrier for this product.
     */
    public $defaultCarrierCode;

    /**
     * @var string|null The default domestic shipping service for this product.
     */
    public $defaultServiceCode;

    /**
     * @var string|null The default domestic packageType for this product.
     */
    public $defaultPackageCode;

    /**
     * @var string|null The default international shipping carrier for
     *                  this product.
     */
    public $defaultIntlCarrierCode;

    /**
     * @var string|null The default international shipping service for
     *                  this product.
     */
    public $defaultIntlServiceCode;

    /**
     * @var string|null The default international packageType for this product.
     */
    public $defaultIntlPackageCode;

    /**
     * @var string|null The default domestic Confirmation type for this Product.
     */
    public $defaultConfirmation;

    /**
     * @var string|null The default international Confirmation type for
     *                  this Product.
     */
    public $defaultIntlConfirmation;

    /**
     * @var string|null The default customs Description for the product.
     */
    public $customsDescription;

    /**
     * @var float|null The default customs Declared Value for the product.
     */
    public $customsValue;

    /**
     * @var string|null The default Harmonized Code for the Product.
     */
    public $customsTariffNo;

    /**
     * @var string|null The default 2 digit ISO Origin Country for the Product.
     */
    public $customsCountryCode;

    /**
     * @var bool|null If true, this product will not be included on
     *                international customs forms.
     */
    public $noCustoms;

    /**
     * @var ProductTag|null The Product Tag used to organize and visually
     *                      identify products. See knowledge base for more
     *                      information on Product Defaults and tags.
     */
    public $tags;


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function datetimeAttributes(): array
    {
        return ['createDate', 'modifyDate'];
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['sku', 'name', 'internalNotes', 'fulfillmentSku', 'createDate', 'modifyDate', 'productType', 'warehouseLocation', 'defaultCarrierCode', 'defaultServiceCode', 'defaultPackageCode', 'defaultIntlCarrierCode', 'defaultIntlServiceCode', 'defaultIntlPackageCode', 'defaultConfirmation', 'defaultIntlConfirmation', 'customsDescription', 'customsTariffNo', 'customsCountryCode'], 'string'],
            [['productId', 'length', 'width', 'height', 'weightOz'], 'number', 'integerOnly' => true],
            [['price', 'defaultCost', 'customsValue'], 'number', 'integerOnly' => false],
            [['active', 'noCustoms'], 'boolean'],
        ];
    }

}
