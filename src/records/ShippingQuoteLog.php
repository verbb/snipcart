<?php
namespace verbb\snipcart\records;

use craft\db\ActiveRecord;
use verbb\snipcart\db\Table;

/**
 * Class ShippingQuoteLog
 *
 * @package verbb\snipcart\records
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
