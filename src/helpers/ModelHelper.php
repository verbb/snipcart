<?php
namespace verbb\snipcart\helpers;

class ModelHelper
{
    // Static Methods
    // =========================================================================

    public static function populateArrayWithModels(array $array, string $class): array
    {
        foreach ($array as &$item) {
            $item = new $class($item);
        }

        return $array;
    }

    public static function safePopulateModel(mixed $data, string $class): mixed
    {
        $cleanData = (array)self::stripUnknownProperties($data, $class);

        return new $class($cleanData);
    }

    public static function safePopulateArrayWithModels(array $array, string $class): array
    {
        foreach ($array as &$item) {
            $item = self::safePopulateModel((array)$item, $class);
        }

        return $array;
    }

    public static function stripUnknownProperties(mixed $data, string $class): mixed
    {
        $model = new $class();

        // get normal model attributes
        $fields = array_keys($model->fields());

        // sometimes models specify dynamic getters and setters that should be treated as normal attributes
        $extraFields = $model->extraFields();

        // combine into one pile of attributes
        $modelAttributes = array_merge($fields, $extraFields);

        // keep a reference of removed properties
        $removed = [];

        if (!is_array($data) && ! is_object($data)) {
            // don’t attempt to loop the un-loopable
            return $data;
        }

        foreach ($data as $key => $value) {
            if (is_string($key) && ! in_array($key, $modelAttributes, false)) {
                if (is_object($data)) {
                    unset($data->{$key});
                } else if (is_array($data)) {
                    unset($data[$key]);
                }

                $removed[] = $key;
            }
        }

        return $data;
    }
}
