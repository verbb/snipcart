<?php
namespace verbb\snipcart\records;

use verbb\snipcart\db\Table;

use craft\db\ActiveRecord;

use DateTime;

class ProductDetails extends ActiveRecord
{
    // Static Methods
    // =========================================================================

    public static function tableName(): string
    {
        return Table::PRODUCT_DETAILS;
    }

    
    // Properties
    // =========================================================================

    public bool $isNew = false;
}
