<?php
namespace modules\snipcartscreenshots;

use DateTime;
use DateTimeZone;
use stdClass;
use verbb\snipcart\models\snipcart\Order;
use verbb\snipcart\services\Orders;

class ScreenshotOrders extends Orders
{
    public function listOrders(int $page = 1, int $limit = 25, array $params = []): stdClass
    {
        $orders = array_slice($this->orders(), ($page - 1) * $limit, $limit);

        return (object)[
            'items' => $orders,
            'totalItems' => count($this->orders()),
            'offset' => ($page - 1) * $limit,
            'limit' => $limit,
        ];
    }

    public function getOrder(string $orderId): ?Order
    {
        foreach ($this->orders() as $order) {
            if ($order->token === $orderId) {
                return $order;
            }
        }

        return null;
    }

    public function getOrderRefunds(string $orderId): array
    {
        return [];
    }

    private function orders(): array
    {
        $rows = [
            ['order-melbourne-1048', 'INV-1048', '2026-09-17 11:42:00', 'Amelia Hart', 'amelia@example.com', 286.00, 'Harbour linen throw', 'LINEN-THROW', 2, 118.00],
            ['order-melbourne-1047', 'INV-1047', '2026-09-16 15:18:00', 'Noah Chen', 'noah@example.com', 164.00, 'Stoneware serving set', 'STONEWARE-SET', 1, 145.00],
            ['order-melbourne-1046', 'INV-1046', '2026-09-15 09:06:00', 'Mia Patel', 'mia@example.com', 92.00, 'Merino travel wrap', 'MERINO-WRAP', 1, 73.00],
            ['order-melbourne-1045', 'INV-1045', '2026-09-13 13:27:00', 'Oliver Nguyen', 'oliver@example.com', 218.00, 'Oak desk organiser', 'OAK-ORGANISER', 2, 99.50],
            ['order-melbourne-1044', 'INV-1044', '2026-09-11 16:03:00', 'Sofia Rossi', 'sofia@example.com', 148.00, 'Wool cushion pair', 'WOOL-CUSHIONS', 1, 129.00],
        ];

        return array_map(static function(array $row): Order {
            [$token, $invoice, $date, $name, $email, $total, $itemName, $sku, $quantity, $unitPrice] = $row;
            $address = [
                'name' => $name,
                'firstName' => explode(' ', $name)[0],
                'address1' => '18 Exhibition Street',
                'city' => 'Melbourne',
                'country' => 'AU',
                'province' => 'VI',
                'postalCode' => '3000',
                'phone' => '+61 3 9000 1234',
                'email' => $email,
            ];

            $order = new Order([
                'token' => $token,
                'creationDate' => new DateTime($date, new DateTimeZone('Australia/Melbourne')),
                'modificationDate' => new DateTime($date, new DateTimeZone('Australia/Melbourne')),
                'completionDate' => new DateTime($date, new DateTimeZone('Australia/Melbourne')),
                'status' => Order::STATUS_PROCESSED,
                'paymentStatus' => Order::PAYMENT_STATUS_PAID,
                'paymentMethod' => Order::PAYMENT_METHOD_CREDIT_CARD,
                'invoiceNumber' => $invoice,
                'email' => $email,
                'cardHolderName' => $name,
                'creditCardLast4Digits' => '4242',
                'shippingAddressSameAsBilling' => true,
                'finalGrandTotal' => $total,
                'shippingFees' => 19.00,
                'shippingMethod' => 'Express courier',
                'paymentTransactionId' => 'txn_demo_' . strtolower(str_replace('-', '_', $invoice)),
                'paymentGatewayUsed' => 'Stripe',
                'cardType' => 'Visa',
                'currency' => 'aud',
                'subtotal' => $total - 19.00,
                'grandTotal' => $total,
                'totalNumberOfItems' => $quantity,
                'billingAddressName' => $address['name'],
                'billingAddressFirstName' => $address['firstName'],
                'billingAddressAddress1' => $address['address1'],
                'billingAddressCity' => $address['city'],
                'billingAddressCountry' => $address['country'],
                'billingAddressProvince' => $address['province'],
                'billingAddressPostalCode' => $address['postalCode'],
                'billingAddressPhone' => $address['phone'],
                'shippingAddressName' => $address['name'],
                'shippingAddressFirstName' => $address['firstName'],
                'shippingAddressAddress1' => $address['address1'],
                'shippingAddressCity' => $address['city'],
                'shippingAddressCountry' => $address['country'],
                'shippingAddressProvince' => $address['province'],
                'shippingAddressPostalCode' => $address['postalCode'],
                'shippingAddressPhone' => $address['phone'],
            ]);
            $order->setItems([[
                    'uniqueId' => $sku . '-1',
                    'id' => $sku,
                    'name' => $itemName,
                    'price' => $unitPrice,
                    'unitPrice' => $unitPrice,
                    'quantity' => $quantity,
                    'totalPrice' => $unitPrice * $quantity,
                    'shippable' => true,
                    'taxable' => true,
                    'customFields' => [],
            ]]);

            return $order;
        }, $rows);
    }
}
