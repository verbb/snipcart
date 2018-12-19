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
     * Take an array of objects and turn each top-level element into an instance
     * of the given data model.
     *
     * @param array   $array  array where each item can be transformed into model
     * @param string  $class  name of desired model class
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
}