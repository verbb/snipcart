<?php

namespace fostercommerce\snipcart\migrations;

use Craft;
use craft\db\Migration;
use craft\db\Query;
use craft\elements\Entry;
use fostercommerce\snipcart\fields\ProductDetails;

/**
 * m230713_081350_migrate_product_details_to_content_table migration.
 */
class m230713_081350_migrate_product_details_to_content_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        // Place migration code here..
    
        $fields = Craft::$app->fields->getFieldTypesWithContent();
               
        // read the contents from each row of the legacy table
        $old = (new Query())
        ->select('*')
        ->from('snipcart_product_details')
        ->all();
        
        foreach($old as $oldItem) {

            $entry = Entry::find()->id($oldItem["elementId"])->one();

            $productDetails = new ProductDetails([
                "defaultShippable" => $oldItem["shippable"],
                "defaultTaxable" => $oldItem["taxable"],
                "defaultWeight" => $oldItem["weight"],
                "defaultWeightUnit" => $oldItem["weightUnit"],
                "defaultLength" => $oldItem["length"],
                "defaultWidth" => $oldItem["width"],
                "defaultHeight" => $oldItem["height"],
                "defaultDimensionsUnit" => $oldItem["dimensionsUnit"],
                "skuDefault" => $oldItem["sku"],
                "name" => $oldItem["sku"],
                "handle" => $oldItem["sku"],

            ]); 

           $isSaved = Craft::$app->getFields()->saveField($productDetails);

            $entry->productDetails = $productDetails;

            dd($isSaved);
            
            $success = Craft::$app->getElements()->saveElement($entry);

            if($success) {
                return true;
            }

            // \Craft::$app->db->createCommand()->insert('content', [
            //     "id" => $oldItem["id"],
            //     "elementId" => $oldItem["elementId"],
            //     "siteId" => $oldItem["siteId"],
            //     "sku" => $oldItem["sku"],
            //     "price" => $oldItem["price"],
            //     "shippable" => $oldItem["shippable"],
            //     "taxable" => $oldItem["taxable"],
            //     "weight" => $oldItem["weight"],
            //     "weightUnit" => $oldItem["weightUnit"],
            //     "length" => $oldItem["length"],
            //     "width" => $oldItem["width"],
            //     "height" => $oldItem["height"],
            //     "dimensionsUnit" => $oldItem["dimensionsUnit"],
            //     "inventory" => $oldItem["inventory"],
            //     "customOptions" => $oldItem["customOptions"]
            // ])->execute();
        }
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
        echo "m230713_081350_migrate_product_details_to_content_table cannot be reverted.\n";
        return false;
    }
}
