<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\helpers;

/**
 * Model utility methods.
 */
class ModelHelper
{
    /**
     * Takes an array of objects and turn each top-level element into an instance
     * of the given data model.
     *
     * @param array   $array  array where each item can be transformed into model
     * @param string  $class  name of desired model class
     *
     * @return array
     */
    public static function populateArrayWithModels(array $array, $class): array
    {
        foreach ($array as &$item)
        {
            $item = new $class($item);
        }

        return $array;
    }

    /**
     * Strips root-level properties from an object if they aren't attributes on
     * the designated model.
     *
     * @param object $data       Object with data, like a webhook payload.
     * @param string $modelClass Base class to be populated, which can't receive any
     *                           unknown attributes.
     * @return object
     * @throws
     */
    public static function stripUnknownProperties($data, $modelClass)
    {
        // instantiate the model so we can poke at it
        $model = new $modelClass;

        // get normal model attributes
        $fields = array_keys($model->fields());

        // sometimes models specify dynamic getters and setters that should be treated as normal attributes
        $extraFields = $model->extraFields();

        // combine into one pile of attributes
        $modelAttributes = array_merge($fields, $extraFields);

        // keep a reference of removed properties
        $removed = [];

        foreach ($data as $key => $value)
        {
            if (is_string($key))
            {
                if ( ! in_array($key, $modelAttributes, false))
                {
                    $removed[] = $key;
                    unset($data->{$key});
                }
            }
        }

        if (count($removed) > 0)
        {
            \Craft::warning(sprintf(
                'Removed unknown %s attributes: %s',
                $modelClass,
                implode(', ', $removed)
            ), 'snipcart');
        }

        return $data;
    }
}