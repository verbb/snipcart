<?php
namespace verbb\snipcart\migrations;

use verbb\snipcart\fields\ProductDetails;

use Craft;
use craft\db\Migration;
use craft\db\Table;

class m231210_000000_verbb_migration extends Migration
{
    // Public Methods
    // =========================================================================

    public function safeUp(): bool
    {
        $this->update(Table::FIELDS, ['type' => ProductDetails::class], ['type' => 'fostercommerce\snipcart\fields\ProductDetails']);
        $this->update(Table::FIELDS, ['type' => ProductDetails::class], ['type' => 'workingconcept\snipcart\fields\ProductDetails']);

        // Don't make the same config changes twice
        $projectConfig = Craft::$app->getProjectConfig();
        $schemaVersion = $projectConfig->get('plugins.snipcart.schemaVersion', true);

        if (version_compare($schemaVersion, '1.1.0', '>=')) {
            return true;
        }

        $fields = $projectConfig->get('fields') ?? [];

        $fieldMap = [
            'workingconcept\\snipcart\\fields\\ProductDetails' => ProductDetails::class,
            'fostercommerce\\snipcart\\fields\\ProductDetails' => ProductDetails::class,
        ];

        foreach ($fields as $fieldUid => $field) {
            $type = $field['type'] ?? null;

            if (isset($fieldMap[$type])) {
                $field['type'] = $fieldMap[$type];

                $projectConfig->set('fields.' . $fieldUid, $field);
            }
        }

        return true;
    }

    public function safeDown(): bool
    {
        echo "m231210_000000_verbb_migration cannot be reverted.\n";
        return false;
    }
}