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
        foreach ($array as &$item) {
            $item = new $class($item);
        }

        return $array;
    }

    /**
     * Cleans the provided data removing any attributes not found on a given
     * class, then uses that clean data to populate an instance of the class.
     *
     * @param mixed   $data   Data to be used to populate the model.
     * @param string  $class  Model to be populated.
     *
     * @return mixed
     */
    public static function safePopulateModel($data, $class)
    {
        $cleanData = self::stripUnknownProperties($data, $class);

        return new $class($cleanData);
    }

    /**
     * Cleans each object in an array and uses the clean data to populate
     * the provided class.
     *
     * @param array  $array  Array in which each item contains data for
     *                       populating a model.
     * @param string $class  Model to be populated.
     *
     * @return array
     */
    public static function safePopulateArrayWithModels(array $array, $class): array
    {
        foreach ($array as &$item) {
            $item = self::safePopulateModel($item, $class);
        }

        return $array;
    }

    /**
     * Strips root-level properties from an object if they aren’t attributes on
     * the designated model.
     *
     * @param object $data   Object with data, like a webhook payload.
     * @param string $class  Model to be populated, which can't receive any
     *                       unknown attributes.
     * @return object
     * @throws
     */
    public static function stripUnknownProperties($data, $class)
    {
        // instantiate the model so we can poke at it
        $model = new $class;

        // get normal model attributes
        $fields = array_keys($model->fields());

        // sometimes models specify dynamic getters and setters that should be treated as normal attributes
        $extraFields = $model->extraFields();

        // combine into one pile of attributes
        $modelAttributes = array_merge($fields, $extraFields);

        // keep a reference of removed properties
        $removed = [];

        if (! is_array($data) && ! is_object($data)) {
            // don’t attempt to loop the un-loopable
            return $data;
        }

        foreach ($data as $key => $value) {
            if (is_string($key) && ! in_array($key, $modelAttributes, false)) {
                if (is_object($data)) {
                    unset($data->{$key});
                } elseif (is_array($data)) {
                    unset($data[$key]);
                }

                $removed[] = $key;
            }
        }

        if (count($removed) > 0) {
            \Craft::warning(sprintf(
                'Removed unknown %s attributes: %s',
                $class,
                implode(', ', $removed)
            ), 'snipcart');
        }

        return $data;
    }
}
