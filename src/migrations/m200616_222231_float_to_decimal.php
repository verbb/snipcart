<?php
namespace verbb\snipcart\migrations;

use verbb\snipcart\db\Table;

use craft\db\Migration;

class m200616_222231_float_to_decimal extends Migration
{
    // Public Methods
    // =========================================================================

    public function safeUp(): void
    {
        if ($this->getDb()->tableExists(Table::PRODUCT_DETAILS)) {
            $this->alterColumn(Table::PRODUCT_DETAILS, 'price', $this->decimal(14, 2)->unsigned());
            $this->alterColumn(Table::PRODUCT_DETAILS, 'weight', $this->decimal(12, 2)->unsigned());
            $this->alterColumn(Table::PRODUCT_DETAILS, 'length', $this->decimal(12, 2)->unsigned());
            $this->alterColumn(Table::PRODUCT_DETAILS, 'width', $this->decimal(12, 2)->unsigned());
            $this->alterColumn(Table::PRODUCT_DETAILS, 'height', $this->decimal(12, 2)->unsigned());
        }
    }

    public function safeDown(): bool
    {
        echo "m200616_222231_float_to_decimal cannot be reverted.\n";
        return false;
    }
}
