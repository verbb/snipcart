<?php

namespace workingconcept\snipcart\migrations;

use Craft;
use craft\db\Migration;

/**
 * m190124_224332_add_inventory_field migration.
 */
class m190124_224332_add_inventory_field extends Migration
{
    public $tableName = '{{%snipcart_product_details}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(
            $this->tableName,
            'inventory',
            $this->integer()
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m190124_224332_add_inventory_field cannot be reverted.\n";
        return false;
    }
}
