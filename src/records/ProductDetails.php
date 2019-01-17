<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\records;

use craft\db\ActiveRecord;

/**
 * Class ProductDetails
 *
 * @package workingconcept\snipcart\records
 * @property int $siteId
 * @property int $elementId
 * @property int $fieldId
 */
class ProductDetails extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%snipcart_product_details}}';
    }
}
