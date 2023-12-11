<?php
namespace verbb\snipcart\migrations;

use craft\db\Migration;
use craft\db\Table;

class m210122_204103_update_namespace extends Migration
{
    // Public Methods
    // =========================================================================

    public function safeUp(): bool
    {
        $this->update(Table::FIELDS, [
            'type' => 'fostercommerce\snipcart\fields\ProductDetails',
        ], [
            'type' => 'workingconcept\snipcart\fields\ProductDetails',
        ]);

        return true;
    }

    public function safeDown(): bool
    {
        echo "m210122_204103_update_namespace cannot be reverted.\n";
        return false;
    }
}
