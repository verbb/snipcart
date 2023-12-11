<?php
namespace verbb\snipcart\services;

use verbb\snipcart\helpers\ModelHelper;
use verbb\snipcart\models\snipcart\Customer;
use verbb\snipcart\models\snipcart\Order;
use verbb\snipcart\Snipcart;

use craft\base\Component;

use Exception;
use stdClass;

class Customers extends Component
{
    // Public Methods
    // =========================================================================

    public function listCustomers(int $page = 1, int $limit = 20, array $params = []): array|stdClass|null
    {
        $params['offset'] = ($page - 1) * $limit;
        $params['limit'] = $limit;

        $customerData = Snipcart::$plugin->getApi()->get('customers', $params);
        $customerData->items = ModelHelper::safePopulateArrayWithModels((array)$customerData->items, Customer::class);

        return $customerData;
    }

    public function getCustomer(string $customerId): ?Customer
    {
        if ($customerData = Snipcart::$plugin->getApi()->get(sprintf('customers/%s', $customerId))) {
            return ModelHelper::safePopulateModel((array)$customerData, Customer::class);
        }

        return null;
    }

    public function getCustomerOrders(string $customerId): array
    {
        $orders = ModelHelper::safePopulateArrayWithModels((array)Snipcart::$plugin->getApi()->get("customers/$customerId/orders", [
            'orderBy' => 'creationDate',
        ]),
        Order::class);

        usort($orders, fn($a, $b): bool => $this->sortOrdersByDateDescending($a, $b));

        return $orders;
    }


    // Private Methods
    // =========================================================================

    private function sortOrdersByDateDescending($a, $b): bool
    {
        return $a->creationDate->getTimestamp() < $b->creationDate->getTimestamp();
    }
}
