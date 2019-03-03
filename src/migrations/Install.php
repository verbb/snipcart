<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\migrations;

use Craft;
use craft\db\Migration;
use workingconcept\snipcart\models\ProductDetails;
use workingconcept\snipcart\controllers\WebhooksController;

/**
 * m181205_000036_api_log migration.
 */
class Install extends Migration
{
    // Public Properties
    // =========================================================================

    public $webhookLogTable     = '{{%snipcart_webhook_log}}';
    public $shippingQuotesTable = '{{%snipcart_shipping_quotes}}';
    public $productDetailsTable = '{{%snipcart_product_details}}';


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->_createTables();
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {

    }


    // Private Methods
    // =========================================================================

    private function _createTables()
    {
        if ( ! $this->getDb()->tableExists($this->webhookLogTable))
        {
            $typeValues = array_keys(WebhooksController::WEBHOOK_EVENT_MAP);

            $this->createTable($this->webhookLogTable, [
                'id'          => $this->primaryKey(),
                'siteId'      => $this->integer(),
                'type'        => $this->enum('type', $typeValues),
                'mode'        => $this->enum('mode', ['live', 'test']),
                'body'        => $this->longText(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid'         => $this->uid(),
            ]);

            $this->createIndex(null, $this->webhookLogTable, ['siteId']);
            $this->addForeignKey(null, $this->webhookLogTable, ['siteId'], '{{%sites}}', ['id'], 'CASCADE');
        }

        if ( ! $this->getDb()->tableExists($this->shippingQuotesTable))
        {
            $this->createTable($this->shippingQuotesTable, [
                'id'          => $this->primaryKey(),
                'siteId'      => $this->integer(),
                'token'       => $this->text(),
                'body'        => $this->mediumText(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid'         => $this->uid(),
            ]);

            $this->createIndex(null, $this->shippingQuotesTable, ['siteId']);
            $this->addForeignKey(null, $this->shippingQuotesTable, ['siteId'], '{{%sites}}', ['id'], 'CASCADE');
        }

        if ( ! $this->getDb()->tableExists($this->productDetailsTable))
        {
            $weightUnitOptions = array_keys(
                ProductDetails::getWeightUnitOptions()
            );

            $dimensionsUnitOptions = array_keys(
                ProductDetails::getDimensionsUnitOptions()
            );

            $this->createTable($this->productDetailsTable, [
                'id'             => $this->primaryKey(),
                'elementId'      => $this->integer()->notNull(),
                'fieldId'        => $this->integer()->notNull(),
                'siteId'         => $this->integer(),
                'sku'            => $this->string()->notNull(),
                'price'          => $this->float(),
                'shippable'      => $this->boolean(),
                'taxable'        => $this->boolean(),
                'weight'         => $this->float(),
                'weightUnit'     => $this->enum('weightUnit', $weightUnitOptions),
                'length'         => $this->float(),
                'width'          => $this->float(),
                'height'         => $this->float(),
                'dimensionsUnit' => $this->enum('dimensionsUnit', $dimensionsUnitOptions),
                'inventory'      => $this->integer(),
                'customOptions'  => $this->longText(),
                'dateCreated'    => $this->dateTime()->notNull(),
                'dateUpdated'    => $this->dateTime()->notNull(),
                'uid'            => $this->uid(),
            ]);

            $this->createIndex(null, $this->productDetailsTable, ['elementId']);
            $this->createIndex(null, $this->productDetailsTable, ['fieldId']);
            $this->createIndex(null, $this->productDetailsTable, ['siteId']);

            $this->addForeignKey(null, $this->productDetailsTable, ['elementId'], '{{%elements}}', ['id'], 'CASCADE');
            $this->addForeignKey(null, $this->productDetailsTable, ['fieldId'], '{{%fields}}', ['id'], 'CASCADE');
            $this->addForeignKey(null, $this->productDetailsTable, ['siteId'], '{{%sites}}', ['id'], 'CASCADE');
        }
    }
}
