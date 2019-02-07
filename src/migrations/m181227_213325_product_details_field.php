<?php

namespace workingconcept\snipcart\migrations;

use workingconcept\snipcart\models\ProductDetails;
use craft\db\Migration;

/**
 * m181227_213325_product_details_field migration.
 */
class m181227_213325_product_details_field extends Migration
{
    public $tableName = '{{%snipcart_product_details}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        if ( ! $this->getDb()->tableExists($this->tableName))
        {
            $weightUnitOptions = array_keys(
                ProductDetails::getWeightUnitOptions()
            );

            $dimensionsUnitOptions = array_keys(
                ProductDetails::getDimensionsUnitOptions()
            );

            $this->createTable($this->tableName, [
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
                'customOptions'  => $this->longText(),
                'dateCreated'    => $this->dateTime()->notNull(),
                'dateUpdated'    => $this->dateTime()->notNull(),
                'uid'            => $this->uid(),
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m181227_213325_product_details_field cannot be reverted.\n";
        return false;
    }

}
