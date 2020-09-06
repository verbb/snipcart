<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2019 Working Concept Inc.
 */

namespace workingconcept\snipcart\helpers;

use DateTimeImmutable;
use workingconcept\snipcart\models\Settings;
use yii\base\InvalidConfigException;

class FormatHelper
{
    /**
     * Returns Snipcart currency formatted with the appropriate symbol,
     * if possible. If the supplied value already has a currency symbol,
     * it will not be changed.
     *
     * @param mixed  $value        The value to be formatted.
     * @param string $currencyType Optional string representing desired currency
     *                             to be explicitly set.
     *
     * @return string
     * @throws InvalidConfigException if no currency is given and [[currencyCode]] is not defined.
     */
    public static function formatCurrency($value, $currencyType = null): string
    {
        if (is_string($value)) {
            $includesSymbol = self::containsSupportedCurrencySymbol($value);

            if ($currencyType !== null) {
                $includesSpecifiedSymbol = self::containsSupportedCurrencySymbol(
                    $value,
                    $currencyType
                );
            }

            if (
                $includesSymbol &&
                ($currencyType === null || $includesSpecifiedSymbol)
            ) {
                return $value;
            }

            $value = self::normalizeCurrencyValue($value);
        }

        return \Craft::$app->formatter->asCurrency($value, $currencyType);
    }

    /**
     * Returns `true` if the provided string contains supported `$currencyType`
     * if provided or *any* supported currency type symbol otherwise.
     *
     * @param  string       $value
     * @param  string|null  $currencyType
     *
     * @return bool
     */
    public static function containsSupportedCurrencySymbol($value, $currencyType = null): bool
    {
        $supportedSymbols = Settings::getCurrencySymbols();

        if ($currencyType) {
            if ( ! array_key_exists($currencyType, $supportedSymbols)) {
                return false;
            }

            return strpos($value, $supportedSymbols[$currencyType]) !== false;
        }

        foreach ($supportedSymbols as $currency => $symbol) {
            if (strpos($value, $symbol) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns relative age of the provided date as short string, with
     * emphasis on compact display rather than precision.
     *
     * - `3y`
     * - `2m`
     * - `12d`
     * - `2h` or `1h`
     * - `<1h` (no minutes or seconds)
     *
     * @param  \DateTime  $date
     *
     * @return string
     */
    public static function tinyDateInterval(\DateTime $date): string
    {
        $now = new DateTimeImmutable();
        $interval = $now->diff($date);

        if ($interval->y > 0) {
            return $interval->y . 'y';
        }

        if ($interval->m > 0) {
            return $interval->m . 'm';
        }

        if ($interval->d > 0) {
            return $interval->d . 'd';
        }

        if ($interval->h >= 1) {
            return $interval->h . 'h';
        }

        return '<1h';
    }

    /**
     * Strip anything other than numbers and decimals from the provided string.
     *
     * @param $value
     *
     * @return string|string[]|null
     */
    private static function normalizeCurrencyValue($value) {
        return preg_replace(
            "/[^0-9\.]/",
            "",
            $value
        );
    }
}
