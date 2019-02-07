<?php

namespace workingconcept\snipcart\migrations;

use craft\db\Migration;

/**
 * Add the indexes and foreign keys we failed to include earlier.
 */
class m190207_194024_indexes_keys_mode extends Migration
{
    public $webhookLogTable = '{{%snipcart_webhook_log}}';
    public $productDetailsTable = '{{%snipcart_product_details}}';
    public $shippingQuotesTable = '{{%snipcart_shipping_quotes}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(
            $this->webhookLogTable,
            'mode',
            $this->enum('mode', ['live', 'test'])
        );

        $this->_addIndexes();
        $this->_addForeignKeys();
    }

    private function _addIndexes()
    {
        $this->createIndex(null, $this->productDetailsTable, ['elementId'], false);
        $this->createIndex(null, $this->productDetailsTable, ['fieldId'], false);
        $this->createIndex(null, $this->productDetailsTable, ['siteId'], false);
        $this->createIndex(null, $this->shippingQuotesTable, ['siteId'], false);
        $this->createIndex(null, $this->webhookLogTable, ['siteId'], false);
    }

    private function _addForeignKeys()
    {
        $this->addForeignKey(null, $this->productDetailsTable, ['elementId'], '{{%elements}}', ['id'], 'CASCADE', null);
        $this->addForeignKey(null, $this->productDetailsTable, ['fieldId'], '{{%fields}}', ['id'], 'CASCADE', null);
        $this->addForeignKey(null, $this->productDetailsTable, ['siteId'], '{{%sites}}', ['id'], 'CASCADE', null);
        $this->addForeignKey(null, $this->shippingQuotesTable, ['siteId'], '{{%sites}}', ['id'], 'CASCADE', null);
        $this->addForeignKey(null, $this->webhookLogTable, ['siteId'], '{{%sites}}', ['id'], 'CASCADE', null);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m190207_194024_indexeskeys_testmode cannot be reverted.\n";
        return false;
    }
}
