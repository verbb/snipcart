<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\records;

use craft\db\ActiveRecord;
use fostercommerce\snipcart\db\Table;

/**
 * Class ProductDetails
 *
 * @package fostercommerce\snipcart\records
 *
 * @property int       $id
 * @property int       $elementId
 * @property int       $fieldId
 * @property int       $siteId
 * @property string    $sku
 * @property float     $price
 * @property bool      $shippable
 * @property bool      $taxable
 * @property float     $weight
 * @property string    $weightUnit
 * @property float     $length
 * @property float     $width
 * @property float     $height
 * @property string    $dimensionsUnit
 * @property int       $inventory
 * @property string    $customOptions
 * @property \DateTime $dateCreated
 * @property \DateTime $dateUpdated
 * @property string    $uid
 */
class ProductDetails extends ActiveRecord
{
    /**
     * @var bool Whether the record has been created but not saved.
     */
    public $isNew = false;

    public static function tableName(): string
    {
        return Table::PRODUCT_DETAILS;
    }
}
