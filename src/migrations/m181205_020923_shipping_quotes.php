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

/**
 * m181205_020923_shipping_quotes migration.
 */
class m181205_020923_shipping_quotes extends Migration
{
    
    public $tableName = '{{%snipcart_shipping_quotes}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        if ( ! $this->getDb()->tableExists($this->tableName))
        {
            $this->createTable($this->tableName, [
                'id'          => $this->primaryKey(),
                'siteId'      => $this->integer(),
                'token'       => $this->text(),
                'body'        => $this->mediumText(),
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
        echo "m181205_020923_shipping_quotes cannot be reverted.\n";
        return false;
    }
}
