<?php

namespace workingconcept\snipcart\migrations;

use craft\db\Migration;

/**
 * m200616_222231_float_to_decimal migration.
 *
 * Converts imprecise float storage to decimal format.
 */
class m200616_222231_float_to_decimal extends Migration
{
    public $productDetailsTable = '{{%snipcart_product_details}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        if ($this->getDb()->tableExists($this->productDetailsTable)) {
            $this->alterColumn(
                $this->productDetailsTable,
                'price',
                $this->decimal(14, 2)->unsigned()
            );

            $this->alterColumn(
                $this->productDetailsTable,
                'weight',
                $this->decimal(12, 2)->unsigned()
            );

            $this->alterColumn(
                $this->productDetailsTable,
                'length',
                $this->decimal(12, 2)->unsigned()
            );

            $this->alterColumn(
                $this->productDetailsTable,
                'width',
                $this->decimal(12, 2)->unsigned()
            );

            $this->alterColumn(
                $this->productDetailsTable,
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
