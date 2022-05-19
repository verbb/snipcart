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
 * Class ShippingQuoteLog
 *
 * @package fostercommerce\snipcart\records
 *
 * @property int $siteId
 * @property string $token
 * @property string $body
 */
class ShippingQuoteLog extends ActiveRecord
{
    public static function tableName(): string
    {
        return Table::SHIPPING_QUOTES;
    }
}
