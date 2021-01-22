<?php

namespace fostercommerce\snipcart\migrations;

use craft\db\Migration;
use fostercommerce\snipcart\db\Table;

/**
 * m200616_222231_float_to_decimal migration.
 *
 * Converts imprecise float storage to decimal format.
 */
class m200616_222231_float_to_decimal extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        if ($this->getDb()->tableExists(Table::PRODUCT_DETAILS)) {
            $this->alterColumn(
                Table::PRODUCT_DETAILS,
                'price',
                $this->decimal(14, 2)->unsigned()
            );

            $this->alterColumn(
                Table::PRODUCT_DETAILS,
                'weight',
                $this->decimal(12, 2)->unsigned()
            );

            $this->alterColumn(
                Table::PRODUCT_DETAILS,
                'length',
                $this->decimal(12, 2)->unsigned()
            );

            $this->alterColumn(
                Table::PRODUCT_DETAILS,
                'width',
                $this->decimal(12, 2)->unsigned()
            );

            $this->alterColumn(
                Table::PRODUCT_DETAILS,
                'height',
                $this->decimal(12, 2)->unsigned()
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        echo "m200616_222231_float_to_decimal cannot be reverted.\n";
        return false;
    }
}
