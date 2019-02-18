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
     * @return
     */
    public static function addFieldTypeToSchema(string $handle, SchemaBuilder $schema)
    {
        $outputSchema = $schema->createObjectType(ucfirst($handle).'FieldData');

        $outputSchema->addFloatField('price')
            ->resolve(function($root) {
                return (float)$root->price;
            });

        $outputSchema->addStringField('sku')
            ->resolve(function($root) {
                return (string)$root->sku;
            });

        $outputSchema->addBooleanField('shippable')
            ->resolve(function($root) {
                return (boolean)$root->shippable;
            });

        $outputSchema->addBooleanField('taxable')
            ->resolve(function($root) {
                return (boolean)$root->taxable;
            });

        $outputSchema->addFloatField('weight')
            ->resolve(function($root) {
                return (float)$root->weight;
            });

        $outputSchema->addStringField('weightUnit')
            ->resolve(function($root) {
                return (string)$root->weightUnit;
            });

        $outputSchema->addFloatField('length')
            ->resolve(function($root) {
                return (float)$root->length;
            });

        $outputSchema->addFloatField('width')
            ->resolve(function($root) {
                return (float)$root->width;
            });

        $outputSchema->addFloatField('height')
            ->resolve(function($root) {
                return (float)$root->height;
            });

        $outputSchema->addStringField('dimensionsUnit')
            ->resolve(function($root) {
                return (string)$root->dimensionsUnit;
            });

        $outputSchema->addIntField('inventory')
            ->resolve(function($root) {
                return (int)$root->inventory;
            });

        return $outputSchema;
    }

}