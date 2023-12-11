<?php
namespace verbb\snipcart\migrations;

use craft\db\Migration;
use craft\db\Table;

class m231210_000000_update_namespace extends Migration
{
    // Public Methods
    // =========================================================================

    public function safeUp(): bool
    {
        $this->update(Table::FIELDS, [
            'type' => 'verbb\snipcart\fields\ProductDetails',
        ], [
            'type' => 'fostercommerce\snipcart\fields\ProductDetails',
        ]);

        return true;
    }

    public function safeDown(): bool
    {
        echo "m231210_000000_update_namespace cannot be reverted.\n";
        return false;
    }
}
