<?php
namespace verbb\snipcart\models\snipcart;

use craft\base\Model;
use DateTime;

class PaymentSchedule extends Model
{
    // Properties
    // =========================================================================

    public string|bool|null $interval = null;
    public ?int $intervalCount = null;
    public ?int $trialPeriodInDays = null;
    public ?DateTime $startsOn = null;


    // Public Methods
    // =========================================================================

    public function getIntervalLabel(): string
    {
        if ($this->intervalCount === 0) {
            return '';
        }

        $label = '';

        if ($this->intervalCount > 1) {
            $label .= $this->intervalCount . ' ';
        }

        $label .= strtolower($this->interval);

        if ($this->intervalCount !== 1) {
            $label .= 's';
        }

        return $label;
    }
}
