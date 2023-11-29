<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\helpers;

use markhuot\CraftQL\Builders\Schema as SchemaBuilder;

class CraftQlHelper
{
    /**
     * Populates a field schema that tells CraftQL how to handle Product Details
     * field information.
     *
     * @param string        $handle Field handle
     * @param SchemaBuilder $schemaBuilder Schema instance
     *
     * @return SchemaBuilder
     */
    public static function addFieldTypeToSchema(string $handle, SchemaBuilder $schemaBuilder): mixed // @phpstan-ignore-line
    {
        $outputSchema = $schemaBuilder->createObjectType(ucfirst($handle) . 'FieldData');

        $outputSchema->addFloatField('price')
            ->resolve(static fn($root): float => (float) $root->price);

        $outputSchema->addStringField('sku')
            ->resolve(static fn($root): string => (string) $root->sku);

        $outputSchema->addBooleanField('shippable')
            ->resolve(static fn($root): bool => (bool) $root->shippable);

        $outputSchema->addBooleanField('taxable')
            ->resolve(static fn($root): bool => (bool) $root->taxable);

        $outputSchema->addFloatField('weight')
            ->resolve(static fn($root): float => (float) $root->weight);

        $outputSchema->addStringField('weightUnit')
            ->resolve(static fn($root): string => (string) $root->weightUnit);

        $outputSchema->addFloatField('length')
            ->resolve(static fn($root): float => (float) $root->length);

        $outputSchema->addFloatField('width')
            ->resolve(static fn($root): float => (float) $root->width);

        $outputSchema->addFloatField('height')
            ->resolve(static fn($root): float => (float) $root->height);

        $outputSchema->addStringField('dimensionsUnit')
            ->resolve(static fn($root): string => (string) $root->dimensionsUnit);

        $outputSchema->addIntField('inventory')
            ->resolve(static fn($root): int => (int) $root->inventory);

        return $outputSchema;
    }
}
