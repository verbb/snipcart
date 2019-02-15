<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\helpers;

/**
 * Measurement conversion methods.
 */
class MeasurementHelper
{
    /**
     * @param float $pounds
     * @return float
     */
    public static function poundsToGrams(float $pounds): float
    {
        return $pounds * 453.592;
    }

    /**
     * @param float $ounces
     * @return float
     */
    public static function ouncesToGrams(float $ounces): float
    {
        return $ounces * 28.3495;
    }

    /**
     * @param float $inches
     * @return float
     */
    public static function inchesToCentimeters(float $inches): float
    {
        return $inches * 2.54;
    }
}