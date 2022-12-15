<?php

namespace fostercommerce\snipcart\migrations;

use Craft;
use craft\db\Migration;
use craft\db\Query;

/**
 * m220816_153427_switch_to_multi_column_field migration.
 */
class m220816_153427_switch_to_multi_column_field extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        // Place migration code here..
    
        $fields = Craft::$app->fields->getFieldTypesWithContent();
        
        Craft::dd($fields);
       
        // read the contents from each row of the legacy table
        $old = (new Query())
        ->select('*')
        ->from('snipcart_product_details')
        ->all();
        
        Craft::dd($old);
        // get the relevant channel
            // find any snipcart fields
            // get the channel(s) they are on
            
            
        
        // for each item in the snipcart_product_details table
        
            // create an entry
            
            // set the field data
            
            // save the entry
            
        // remove the snipcart_product_details table ?
        
        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        echo "m220816_153427_switch_to_multi_column_field cannot be reverted.\n";
        return false;
    }
}
