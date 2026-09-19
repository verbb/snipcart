<?php
namespace modules\snipcartscreenshots;

use DateTime;
use DateTimeZone;
use stdClass;
use verbb\snipcart\models\snipcart\Customer;
use verbb\snipcart\models\snipcart\CustomerStatistics;
use verbb\snipcart\services\Customers;

class ScreenshotCustomers extends Customers
{
    public function listCustomers(int $page = 1, int $limit = 20, array $params = []): stdClass
    {
        $customers = array_slice($this->customers(), ($page - 1) * $limit, $limit);

        return (object)[
            'items' => $customers,
            'totalItems' => count($this->customers()),
            'offset' => ($page - 1) * $limit,
            'limit' => $limit,
        ];
    }

    public function getCustomer(string $customerId): ?Customer
    {
        foreach ($this->customers() as $customer) {
            if ($customer->id === $customerId) {
                return $customer;
            }
        }

        return null;
    }

    private function customers(): array
    {
        $rows = [
            ['customer-amelia', 'Amelia Hart', 'amelia@example.com', '18 Exhibition Street', 8, 1248.00],
            ['customer-noah', 'Noah Chen', 'noah@example.com', '42 Gertrude Street', 6, 936.00],
            ['customer-mia', 'Mia Patel', 'mia@example.com', '7 Rathdowne Street', 5, 742.00],
            ['customer-oliver', 'Oliver Nguyen', 'oliver@example.com', '81 Smith Street', 4, 624.00],
            ['customer-sofia', 'Sofia Rossi', 'sofia@example.com', '26 Lygon Street', 3, 481.00],
        ];

        return array_map(static fn(array $row): Customer => new Customer([
            'id' => $row[0],
            'billingAddressName' => $row[1],
            'billingAddressFirstName' => explode(' ', $row[1])[0],
            'email' => $row[2],
            'billingAddressAddress1' => $row[3],
            'billingAddressCity' => 'Melbourne',
            'billingAddressCountry' => 'AU',
            'billingAddressProvince' => 'VIC',
            'billingAddressPostalCode' => '3000',
            'status' => Customer::STATUS_CONFIRMED,
            'creationDate' => new DateTime('2026-07-12 10:00:00', new DateTimeZone('Australia/Melbourne')),
            'statistics' => new CustomerStatistics([
                'ordersCount' => $row[4],
                'ordersAmount' => $row[5],
            ]),
        ]), $rows);
    }
}
