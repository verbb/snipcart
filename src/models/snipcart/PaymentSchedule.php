<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models\snipcart;

class PaymentSchedule extends \craft\base\Model
{
    /**
     * @var string|bool
     */
    public $interval;

    /**
     * @var int
     */
    public $intervalCount;

    /**
     * @var int|null
     */
    public $trialPeriodInDays;

    /**
     * @var \DateTime
     */
    public $startsOn;

    /**
     * @inheritdoc
     */
    public function datetimeAttributes(): array
    {
        return ['startsOn'];
    }

    /**
     * Returns a formatted string describing the description interval, meant to
     * come after a word like “every”.
     *
     * Omits `1` and pluralizes units.
     *
     * Examples:
     *
     * - `week`
     * - `2 weeks`
     * - `3 months`
     * - `year`
     *
     * @return string
     */
    public function getIntervalLabel(): string
    {
        if (!$this->intervalCount) {
            return "";
        }

        $label = "";

        if ($this->intervalCount > 1) {
            $label .= $this->intervalCount . " ";
        }

        $label .= strtolower($this->interval);

        if ($this->intervalCount !== 1) {
            $label .= "s";
        }

        return $label;
    }
}
