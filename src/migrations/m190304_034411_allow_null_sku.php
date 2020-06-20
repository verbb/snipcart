<?php

namespace workingconcept\snipcart\migrations;

use craft\db\Migration;

/**
 * m190304_034411_allow_null_sku migration.
 */
class m190304_034411_allow_null_sku extends Migration
{
    // Public Properties
    // =========================================================================

    public $productDetailsTable = '{{%snipcart_product_details}}';


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        if ($this->getDb()->tableExists($this->productDetailsTable)) {
            /**
             * Allow storing null in the column to keep from causing an Element
             * re-save failure after adding a new Product Details field
             * to a Section.
             */
            $this->alterColumn(
                $this->productDetailsTable,
                'sku',
                $this->string()->null()
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m190304_034411_allow_null_sku cannot be reverted.\n";
        return false;
    }
}
