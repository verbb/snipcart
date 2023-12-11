<?php
namespace verbb\snipcart\services;

use verbb\snipcart\Snipcart;

use craft\base\Component;

use DateTime;
use stdClass;
use Exception;

class Data extends Component
{
    // Public Methods
    // =========================================================================

    public function getOrderCount(DateTime|int $from, DateTime|int $to): array|stdClass|null
    {
        return Snipcart::$plugin->getApi()->get('data/orders/count', [
            'from' => $this->prepDate($from),
            'to' => $this->prepDate($to),
        ]);
    }

    public function getPerformance(DateTime|int $from, DateTime|int $to): array|stdClass|null
    {
        return Snipcart::$plugin->getApi()->get('data/performance', [
            'from' => $this->prepDate($from),
            'to' => $this->prepDate($to),
        ]);
    }

    public function getSales(DateTime|int $from, DateTime|int $to): array|stdClass|null
    {
        return Snipcart::$plugin->getApi()->get('data/orders/sales', [
            'from' => $this->prepDate($from),
            'to' => $this->prepDate($to),
        ]);
    }


    // Private Methods
    // =========================================================================

    private function prepDate(DateTime|int $date): string
    {
        if ($date instanceof DateTime) {
            return $date->format('U');
        }

        return (string) $date;
    }
}
