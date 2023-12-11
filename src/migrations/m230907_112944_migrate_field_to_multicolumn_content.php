<?php
namespace verbb\snipcart\migrations;

use Craft;
use craft\db\Migration;
use craft\db\Query;
use craft\elements\Entry;
use craft\helpers\App;
use craft\helpers\Json;

use RuntimeException;

class m230907_112944_migrate_field_to_multicolumn_content extends Migration
{
    // Public Methods
    // =========================================================================

    public function safeUp(): bool
    {
        $this->resaveFields();

        $productData = (new Query())
            ->select('*')
            ->from('{{%snipcart_product_details}}')
            ->where([
                'not', [
                    'sku' => '',

                ], ])
            ->all();

        $fieldsService = Craft::$app->getFields();

        foreach ($productData as $product) {
            // find the entry it is on
            $element = (new Query())
                ->select('canonicalId')
                ->from('{{%elements}}')
                ->where([
                    'id' => $product['elementId'],
                ])
                ->one();

            $entry = Entry::find()->id($element['canonicalId'])->one();
            $field = $fieldsService->getFieldById($product['fieldId']);
            
            // set the field data
            if (!$entry) {
                continue;
            }

            if (!$field) {
                continue;
            }

            $entry->setFieldValue($field->handle, [
                'sku' => $product['sku'],
                'price' => $product['price'],
                'shippable' => $product['shippable'],
                'taxable' => $product['taxable'],
                'weight' => $product['weight'],
                'length' => $product['length'],
                'width' => $product['width'],
                'height' => $product['height'],
                'dimensionsUnit' => $product['dimensionsUnit'],
                'inventory' => $product['inventory'],
                'customOptions' => $product['customOptions'],
            ]);

            Craft::$app->elements->saveElement($entry, false);
        }

        return true;
    }

    public function safeDown(): bool
    {
        echo "m220816_153427_switch_to_multi_column_field cannot be reverted.\n";
        return false;
    }

    public function resaveFields(): void
    {
        // We need to resave **all** fields because we don't know where fields are being added.
        // For example, they could be in Matrix fields, Super Table fields, etc.
        // We need to rely on those fields resaving correctly to implement the migration.

        App::maxPowerCaptain(); // We could potentially be resaving a lot of fields.

        $fields = (new Query())
            ->from('{{%fields}}')
            ->all();

        $fieldsService = Craft::$app->getFields();
        $fieldsService->refreshFields();

        foreach ($fields as $field) {
            $field = $fieldsService->getFieldById($field['id']);

            if (!$field) {
                continue;
            }

            if (!$fieldsService->saveField($field, false)) {
                throw new RuntimeException(Json::encode([
                    'field' => $field->handle,
                    'type' => $field::class,
                    'errors' => $field->getErrors(),
                ]));
            }
        }
    }
}
