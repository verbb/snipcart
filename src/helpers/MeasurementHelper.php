<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\helpers;

/**
 * Measurement conversion methods.
 */
class MeasurementHelper
{
    /**
     * Converts pounds to grams.
     */
    public static function poundsToGrams(float $pounds): float
    {
        return $pounds * 453.592;
    }

    /**
     * Converts ounces to grams.
     */
    public static function ouncesToGrams(float $ounces): float
    {
        return $ounces * 28.3495;
    }

    /**
     * Converts inches to centimeters.
     */
    public static function inchesToCentimeters(float $inches): float
    {
        return $inches * 2.54;
    }
}
