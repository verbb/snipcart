<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\helpers;

use craft\base\Element;
use craft\models\FieldLayout;
use fostercommerce\snipcart\fields\ProductDetails;

/**
 * Field utility methods.
 */
class FieldHelper
{
    /**
     * Returns product info for the provided Element regardless of the
     * field handle.
     *
     * @param Element $element
     *
     * @return ProductDetails|null
     */
    public static function getProductInfo($element)
    {
        // if we don't have an Element, there’s nothing to get
        if (! isset($element)) {
            return null;
        }

        if (! ($fieldLayout = $element->getFieldLayout()) instanceof FieldLayout) {
            return null;
        }

        if (($fields = $fieldLayout->getCustomFields()) === []) {
            return null;
        }

        foreach ($fields as $field) {
            if ($field instanceof ProductDetails) {
                return $element->getFieldValue($field->handle);
            }
        }

        return null;
    }

    /**
     * Returns the field handle for the Element’s Product Details field,
     * if it exists.
     *
     * @param Element $element
     */
    public static function getProductInfoFieldHandle($element): ?string
    {
        // if we don't have an Element, there's nothing to get
        if (! isset($element)) {
            return null;
        }

        if (($fieldLayout = $element->getFieldLayout()) instanceof FieldLayout) {
            $fields = $fieldLayout->getCustomFields();

            foreach ($fields as $field) {
                if ($field instanceof ProductDetails) {
                    return $field->handle;
                }
            }
        }

        return null;
    }
}
