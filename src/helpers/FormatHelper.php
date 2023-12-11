<?php
namespace verbb\snipcart\helpers;

use verbb\snipcart\models\Settings;

use Craft;

use DateTime;
use DateTimeImmutable;

class FormatHelper
{
    // Static Methods
    // =========================================================================

    public static function formatCurrency(mixed $value, ?string $currencyType = null): string
    {
        if (is_string($value)) {
            $includesSymbol = self::containsSupportedCurrencySymbol($value);

            if ($currencyType !== null) {
                $includesSpecifiedSymbol = self::containsSupportedCurrencySymbol($value, $currencyType);
            }

            if ($includesSymbol && ($currencyType === null || $includesSpecifiedSymbol)) {
                return $value;
            }

            $value = self::normalizeCurrencyValue($value);
        }

        return Craft::$app->formatter->asCurrency($value, $currencyType);
    }

    public static function containsSupportedCurrencySymbol(string $value, ?string $currencyType = null): bool
    {
        $supportedSymbols = Settings::getCurrencySymbols();

        if ($currencyType) {
            if (!array_key_exists($currencyType, $supportedSymbols)) {
                return false;
            }

            return str_contains($value, $supportedSymbols[$currencyType]);
        }

        foreach ($supportedSymbols as $currency => $symbol) {
            if (str_contains($value, $symbol)) {
                return true;
            }
        }

        return false;
    }

    public static function tinyDateInterval(DateTime $dateTime): string
    {
        $dateTimeImmutable = new DateTimeImmutable();
        $interval = $dateTimeImmutable->diff($dateTime);

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

    private static function normalizeCurrencyValue(mixed $value): string|array|null
    {
        return preg_replace("/[^0-9\.]/", '', $value);
    }
}
