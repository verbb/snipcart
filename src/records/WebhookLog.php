<?php
namespace verbb\snipcart\records;

use verbb\snipcart\db\Table;

use craft\db\ActiveRecord;

class WebhookLog extends ActiveRecord
{
    // Static Methods
    // =========================================================================

    public static function tableName(): string
    {
        return Table::WEBHOOK_LOG;
    }
}
