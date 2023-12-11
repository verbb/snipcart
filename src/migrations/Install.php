<?php
namespace verbb\snipcart\migrations;

use verbb\snipcart\db\Table;
use verbb\snipcart\models\ProductDetails;
use verbb\snipcart\controllers\WebhooksController;
use Craft;
use craft\db\Migration;
use craft\helpers\MigrationHelper;

/**
 * m181205_000036_api_log migration.
 */
class Install extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        $this->createTables();
        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        $this->dropForeignKeys();
        $this->dropTables();
        $this->dropProjectConfig();
        return true;
    }

    private function createTables()
    {
        if (! $this->getDb()->tableExists(Table::WEBHOOK_LOG)) {
            $typeValues = array_keys(WebhooksController::WEBHOOK_EVENT_MAP);

            $this->createTable(Table::WEBHOOK_LOG, [
                'id'          => $this->primaryKey(),
                'siteId'      => $this->integer(),
                'type'        => $this->enum('type', $typeValues),
                'mode'        => $this->enum('mode', ['live', 'test']),
                'body'        => $this->longText(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid'         => $this->uid(),
            ]);

            $this->createIndex(null, Table::WEBHOOK_LOG, ['siteId']);
            $this->addForeignKey(null, Table::WEBHOOK_LOG, ['siteId'], '{{%sites}}', ['id'], 'CASCADE');
        }

        if (! $this->getDb()->tableExists(Table::SHIPPING_QUOTES)) {
            $this->createTable(Table::SHIPPING_QUOTES, [
                'id'          => $this->primaryKey(),
                'siteId'      => $this->integer(),
                'token'       => $this->text(),
                'body'        => $this->mediumText(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid'         => $this->uid(),
            ]);

            $this->createIndex(null, Table::SHIPPING_QUOTES, ['siteId']);
            $this->addForeignKey(null, Table::SHIPPING_QUOTES, ['siteId'], '{{%sites}}', ['id'], 'CASCADE');
        }

        if (! $this->getDb()->tableExists(Table::PRODUCT_DETAILS)) {
            $weightUnitOptions = array_keys(
                ProductDetails::getWeightUnitOptions()
            );

            $dimensionsUnitOptions = array_keys(
                ProductDetails::getDimensionsUnitOptions()
            );

            $this->createTable(Table::PRODUCT_DETAILS, [
                'id'             => $this->primaryKey(),
                'elementId'      => $this->integer()->notNull(),
                'fieldId'        => $this->integer()->notNull(),
                'siteId'         => $this->integer(),
                'sku'            => $this->string(),
                'price'          => $this->decimal(14, 2)->unsigned(),
                'shippable'      => $this->boolean(),
                'taxable'        => $this->boolean(),
                'weight'         => $this->decimal(12, 2)->unsigned(),
                'weightUnit'     => $this->enum('weightUnit', $weightUnitOptions),
                'length'         => $this->decimal(12, 2)->unsigned(),
                'width'          => $this->decimal(12, 2)->unsigned(),
                'height'         => $this->decimal(12, 2)->unsigned(),
                'dimensionsUnit' => $this->enum('dimensionsUnit', $dimensionsUnitOptions),
                'inventory'      => $this->integer(),
                'customOptions'  => $this->longText(),
                'dateCreated'    => $this->dateTime()->notNull(),
                'dateUpdated'    => $this->dateTime()->notNull(),
                'uid'            => $this->uid(),
            ]);

            $this->createIndex(null, Table::PRODUCT_DETAILS, ['elementId']);
            $this->createIndex(null, Table::PRODUCT_DETAILS, ['fieldId']);
            $this->createIndex(null, Table::PRODUCT_DETAILS, ['siteId']);

            $this->addForeignKey(null, Table::PRODUCT_DETAILS, ['elementId'], '{{%elements}}', ['id'], 'CASCADE');
            $this->addForeignKey(null, Table::PRODUCT_DETAILS, ['fieldId'], '{{%fields}}', ['id'], 'CASCADE');
            $this->addForeignKey(null, Table::PRODUCT_DETAILS, ['siteId'], '{{%sites}}', ['id'], 'CASCADE');
        }
    }

    private function dropForeignKeys()
    {
        $tables = [
            Table::WEBHOOK_LOG,
            Table::SHIPPING_QUOTES,
            Table::PRODUCT_DETAILS
        ];

        foreach ($tables as $table) {
            if ($this->getDb()->tableExists($table)) {
                MigrationHelper::dropAllForeignKeysToTable($table, $this);
                MigrationHelper::dropAllForeignKeysOnTable($table, $this);
            }
        }
    }

    private function dropTables()
    {
        $this->dropTableIfExists(Table::WEBHOOK_LOG);
        $this->dropTableIfExists(Table::SHIPPING_QUOTES);
        $this->dropTableIfExists(Table::PRODUCT_DETAILS);
    }

    private function dropProjectConfig()
    {
        Craft::$app->projectConfig->remove('snipcart');
    }
}
