<?php

use workingconcept\snipcart\helpers\FormatHelper;
use workingconcept\snipcart\models\Settings;
use craft\helpers\DateTimeHelper;

class FormatHelperTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testExpectedCurrencyFormats() {
        // input, expected output, currency
        $testValues = [
            ['$1', '$1', Settings::CURRENCY_USD],
            ['$1.00', '$1.00', Settings::CURRENCY_USD],
            [1, '$1.00', Settings::CURRENCY_USD],
            [1, 'CA$1.00', Settings::CURRENCY_CAD],
            [1, '€1.00', Settings::CURRENCY_EUR],
            [1, '£1.00', Settings::CURRENCY_GBP],
            // there can be a non-breaking space after CHF
            [1, ["CHF\xC2\xA01.00", "CHF1.00"], Settings::CURRENCY_CHF],
            ['€1.00', '$1.00', Settings::CURRENCY_USD],
            ['1,000', '$1,000.00', Settings::CURRENCY_USD],
        ];

        foreach ($testValues as list ($input, $expected, $currency)) {
            if (is_array($expected)) {
                $this->assertContains(
                    FormatHelper::formatCurrency($input, $currency),
                    $expected
                );
            } else {
                $this->assertEquals(
                    $expected,
                    FormatHelper::formatCurrency($input, $currency)
                );
            }
        }
    }

    public function testExpectedTinyDateIntervals() {
        $now = DateTimeHelper::currentUTCDateTime();

        // input, expected output, currency
        $testValues = [
            [(clone $now)->sub(DateInterval::createFromDateString('15 minutes')), '<1h'],
            [(clone $now)->sub(DateInterval::createFromDateString('59 minutes')), '<1h'],
            [(clone $now)->sub(DateInterval::createFromDateString('1 hour')), '1h'],
            [(clone $now)->sub(DateInterval::createFromDateString('16 hours')), '16h'],
            [(clone $now)->sub(DateInterval::createFromDateString('36 hours')), '1d'],
            [(clone $now)->sub(DateInterval::createFromDateString('3 days')), '3d'],
            [(clone $now)->sub(DateInterval::createFromDateString('40 days')), '1m'],
            [(clone $now)->sub(DateInterval::createFromDateString('92 days')), '3m'],
            [(clone $now)->sub(DateInterval::createFromDateString('13 months')), '1y'],
            [(clone $now)->sub(DateInterval::createFromDateString('23 months')), '1y'],
            [(clone $now)->sub(DateInterval::createFromDateString('36 months')), '3y'],
        ];

        foreach ($testValues as $testValue) {
            $this->assertEquals(
                FormatHelper::tinyDateInterval($testValue[0]),
                $testValue[1]
            );
        }
    }
}
