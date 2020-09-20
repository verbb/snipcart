<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\records;

use craft\db\ActiveRecord;
use workingconcept\snipcart\db\Table;

/**
 * Class ShippingQuoteLog
 *
 * @package workingconcept\snipcart\records
 *
 * @property int $siteId
 * @property string $token
 * @property string $body
 */
class ShippingQuoteLog extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return Table::SHIPPING_QUOTES;
    }
}
