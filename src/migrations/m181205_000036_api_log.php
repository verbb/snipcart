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
use workingconcept\snipcart\controllers\WebhooksController;

/**
 * m181205_000036_api_log migration.
 */
class m181205_000036_api_log extends Migration
{
    public $tableName = '{{%snipcart_webhook_log}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        if ( ! $this->getDb()->tableExists($this->tableName))
        {
            $typeValues = array_values(WebhooksController::WEBHOOK_EVENTS);

            $this->createTable($this->tableName, [
                'id'          => $this->primaryKey(),
                'siteId'      => $this->integer(),
                'type'        => $this->enum('type', $typeValues),
                'body'        => $this->longText(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid'         => $this->uid(),
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m181205_000036_api_log cannot be reverted.\n";
        
        if ($this->getDb()->tableExists($this->tableName))
        {
            $this->getDb()->deleteTable($this->tableName);
        }
        
        return false;
    }
}
