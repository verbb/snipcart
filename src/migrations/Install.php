<?php
namespace verbb\snipcart\migrations;

use verbb\snipcart\controllers\WebhooksController;
use verbb\snipcart\db\Table;
use verbb\snipcart\models\ProductDetails;

use Craft;
use craft\db\Migration;
use craft\helpers\MigrationHelper;

class Install extends Migration
{
    // Public Methods
    // =========================================================================

    public function safeUp(): bool
    {
        $this->createTables();
        $this->createIndexes();
        $this->addForeignKeys();

        return true;
    }

    public function safeDown(): bool
    {
        $this->dropProjectConfig();
        $this->dropForeignKeys();
        $this->dropTables();

        return true;
    }


    // Private Methods
    // =========================================================================

    private function createTables(): void
    {
        $typeValues = array_keys(WebhooksController::WEBHOOK_EVENT_MAP);

        $this->archiveTableIfExists(Table::WEBHOOK_LOG);
        $this->createTable(Table::WEBHOOK_LOG, [
            'id' => $this->primaryKey(),
            'siteId' => $this->integer(),
            'type' => $this->enum('type', $typeValues),
            'mode' => $this->enum('mode', ['live', 'test']),
            'body' => $this->longText(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        $this->archiveTableIfExists(Table::SHIPPING_QUOTES);
        $this->createTable(Table::SHIPPING_QUOTES, [
            'id' => $this->primaryKey(),
            'siteId' => $this->integer(),
            'token' => $this->text(),
            'body' => $this->mediumText(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        $weightUnitOptions = array_keys(ProductDetails::getWeightUnitOptions());
        $dimensionsUnitOptions = array_keys(ProductDetails::getDimensionsUnitOptions());

        $this->archiveTableIfExists(Table::PRODUCT_DETAILS);
        $this->createTable(Table::PRODUCT_DETAILS, [
            'id' => $this->primaryKey(),
            'elementId' => $this->integer()->notNull(),
            'fieldId' => $this->integer()->notNull(),
            'siteId' => $this->integer(),
            'sku' => $this->string(),
            'price' => $this->decimal(14, 2)->unsigned(),
            'shippable' => $this->boolean(),
            'taxable' => $this->boolean(),
            'weight' => $this->decimal(12, 2)->unsigned(),
            'weightUnit' => $this->enum('weightUnit', $weightUnitOptions),
            'length' => $this->decimal(12, 2)->unsigned(),
            'width' => $this->decimal(12, 2)->unsigned(),
            'height' => $this->decimal(12, 2)->unsigned(),
            'dimensionsUnit' => $this->enum('dimensionsUnit', $dimensionsUnitOptions),
            'inventory' => $this->integer(),
            'customOptions' => $this->longText(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);
    }
    private function createIndexes(): void
    {
        $this->createIndex(null, Table::WEBHOOK_LOG, ['siteId']);
        $this->createIndex(null, Table::SHIPPING_QUOTES, ['siteId']);
        $this->createIndex(null, Table::PRODUCT_DETAILS, ['elementId']);
        $this->createIndex(null, Table::PRODUCT_DETAILS, ['fieldId']);
        $this->createIndex(null, Table::PRODUCT_DETAILS, ['siteId']);
    }

    private function addForeignKeys(): void
    {
        $this->addForeignKey(null, Table::WEBHOOK_LOG, ['siteId'], '{{%sites}}', ['id'], 'CASCADE');
        $this->addForeignKey(null, Table::SHIPPING_QUOTES, ['siteId'], '{{%sites}}', ['id'], 'CASCADE');
        $this->addForeignKey(null, Table::PRODUCT_DETAILS, ['elementId'], '{{%elements}}', ['id'], 'CASCADE');
        $this->addForeignKey(null, Table::PRODUCT_DETAILS, ['fieldId'], '{{%fields}}', ['id'], 'CASCADE');
        $this->addForeignKey(null, Table::PRODUCT_DETAILS, ['siteId'], '{{%sites}}', ['id'], 'CASCADE');
    }

    private function dropForeignKeys(): void
    {
        $tables = [
            Table::WEBHOOK_LOG,
            Table::SHIPPING_QUOTES,
            Table::PRODUCT_DETAILS,
        ];

        foreach ($tables as $table) {
            if ($this->getDb()->tableExists($table)) {
                MigrationHelper::dropAllForeignKeysToTable($table, $this);
                MigrationHelper::dropAllForeignKeysOnTable($table, $this);
            }
        }
    }

    private function dropTables(): void
    {
        $this->dropTableIfExists(Table::WEBHOOK_LOG);
        $this->dropTableIfExists(Table::SHIPPING_QUOTES);
        $this->dropTableIfExists(Table::PRODUCT_DETAILS);
    }

    private function dropProjectConfig(): void
    {
        Craft::$app->projectConfig->remove('snipcart');
    }
}
