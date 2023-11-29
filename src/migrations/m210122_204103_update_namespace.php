<?php

namespace fostercommerce\snipcart\migrations;

use craft\db\Migration;
use craft\db\Table;

/**
 * m210122_204103_update_namespace migration.
 */
class m210122_204103_update_namespace extends Migration
{
    public function safeUp(): bool
    {
        // update namespace for existing fields: `workingconcept/...` → `fostercommerce/...`
        \Craft::$app->db->createCommand()
            ->update(
                Table::FIELDS,
                [
                    'type' => 'fostercommerce\snipcart\fields\ProductDetails',
                ],
                [
                    'type' => 'workingconcept\snipcart\fields\ProductDetails',
                ]
            )
            ->execute();

        return true;
    }

    public function safeDown(): bool
    {
        echo "m210122_204103_update_namespace cannot be reverted.\n";
        return false;
    }
}
