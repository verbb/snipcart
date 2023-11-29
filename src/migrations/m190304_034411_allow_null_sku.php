<?php

namespace fostercommerce\snipcart\migrations;

use craft\db\Migration;
use fostercommerce\snipcart\db\Table;

/**
 * m190304_034411_allow_null_sku migration.
 */
class m190304_034411_allow_null_sku extends Migration
{
    public function safeUp(): void
    {
        if ($this->getDb()->tableExists(Table::PRODUCT_DETAILS)) {
            /**
             * Allow storing null in the column to keep from causing an Element
             * re-save failure after adding a new Product Details field
             * to a Section.
             */
            $this->alterColumn(
                Table::PRODUCT_DETAILS,
                'sku',
                $this->string()->null()
            );
        }
    }

    public function safeDown(): bool
    {
        echo "m190304_034411_allow_null_sku cannot be reverted.\n";
        return false;
    }
}
