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
}
