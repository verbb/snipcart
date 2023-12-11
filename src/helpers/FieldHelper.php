<?php
namespace verbb\snipcart\helpers;

use verbb\snipcart\fields\ProductDetails;

use craft\base\ElementInterface;
use craft\models\FieldLayout;

class FieldHelper
{
    // Static Methods
    // =========================================================================

    public static function getProductInfo(ElementInterface $element): ?ProductDetails
    {
        // if we don't have an Element, there’s nothing to get
        if (!isset($element)) {
            return null;
        }

        if (!($fieldLayout = $element->getFieldLayout()) instanceof FieldLayout) {
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

    public static function getProductInfoFieldHandle(ElementInterface $element): ?string
    {
        // if we don't have an Element, there's nothing to get
        if (!isset($element)) {
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
