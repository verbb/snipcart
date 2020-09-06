<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\helpers;

use markhuot\CraftQL\Builders\Schema as SchemaBuilder;

class CraftQlHelper
{
    /**
     * Populates a field schema that tells CraftQL how to handle Product Details
     * field information.
     *
     * @param string        $handle Field handle
     * @param SchemaBuilder $schema Schema instance
     *
     * @return SchemaBuilder
     */
    public static function addFieldTypeToSchema(string $handle, SchemaBuilder $schema)
    {
        $outputSchema = $schema->createObjectType(ucfirst($handle).'FieldData');

        $outputSchema->addFloatField('price')
            ->resolve(static function ($root) {
                return (float)$root->price;
            });

        $outputSchema->addStringField('sku')
            ->resolve(static function ($root) {
                return (string)$root->sku;
            });

        $outputSchema->addBooleanField('shippable')
            ->resolve(static function ($root) {
                return (boolean)$root->shippable;
            });

        $outputSchema->addBooleanField('taxable')
            ->resolve(static function ($root) {
                return (boolean)$root->taxable;
            });

        $outputSchema->addFloatField('weight')
            ->resolve(static function ($root) {
                return (float)$root->weight;
            });

        $outputSchema->addStringField('weightUnit')
            ->resolve(static function ($root) {
                return (string)$root->weightUnit;
            });

        $outputSchema->addFloatField('length')
            ->resolve(static function ($root) {
                return (float)$root->length;
            });

        $outputSchema->addFloatField('width')
            ->resolve(static function ($root) {
                return (float)$root->width;
            });

        $outputSchema->addFloatField('height')
            ->resolve(static function ($root) {
                return (float)$root->height;
            });

        $outputSchema->addStringField('dimensionsUnit')
            ->resolve(static function ($root) {
                return (string)$root->dimensionsUnit;
            });

        $outputSchema->addIntField('inventory')
            ->resolve(static function ($root) {
                return (int)$root->inventory;
            });

        return $outputSchema;
    }
}
