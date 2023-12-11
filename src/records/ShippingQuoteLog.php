<?php
namespace verbb\snipcart\records;

use verbb\snipcart\db\Table;

use craft\db\ActiveRecord;

class ShippingQuoteLog extends ActiveRecord
{
    // Static Methods
    // =========================================================================

    public static function tableName(): string
    {
        return Table::SHIPPING_QUOTES;
    }
}
