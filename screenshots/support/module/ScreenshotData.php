<?php
namespace modules\snipcartscreenshots;

use DateTime;
use stdClass;
use verbb\snipcart\services\Data;

class ScreenshotData extends Data
{
    public function getOrderCount(DateTime|int $from, DateTime|int $to): stdClass
    {
        return $this->series([3, 5, 4, 7, 6, 9, 8, 11, 8, 12, 10, 14]);
    }

    public function getSales(DateTime|int $from, DateTime|int $to): stdClass
    {
        if ($from instanceof DateTime && $to instanceof DateTime && ($to->getTimestamp() - $from->getTimestamp()) <= 864000) {
            return $this->series([284, 462, 398, 716, 642, 784, 691], 12);
        }

        return $this->series([284, 462, 398, 716, 642, 884, 791, 1088, 846, 1264, 1128, 1486]);
    }

    public function getPerformance(DateTime|int $from, DateTime|int $to): stdClass
    {
        return (object)[
            'ordersCount' => 97,
            'ordersSales' => 12846.00,
            'averageOrdersValue' => 132.43,
            'averageCustomerValue' => 284.91,
            'customers' => (object)[
                'newCustomersCount' => 31,
                'returningCustomersCount' => 14,
            ],
        ];
    }

    private function series(array $values, int $startDay = 1): stdClass
    {
        $rows = [];
        foreach ($values as $index => $value) {
            $rows[] = (object)[
                'name' => sprintf('2026-09-%02d', $index + $startDay),
                'value' => $value,
            ];
        }

        return (object)['data' => $rows];
    }
}
