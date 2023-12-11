<?php
namespace verbb\snipcart\helpers;

class MeasurementHelper
{
    // Static Methods
    // =========================================================================

    public static function poundsToGrams(float $pounds): float
    {
        return $pounds * 453.592;
    }

    public static function ouncesToGrams(float $ounces): float
    {
        return $ounces * 28.3495;
    }

    public static function inchesToCentimeters(float $inches): float
    {
        return $inches * 2.54;
    }
}
