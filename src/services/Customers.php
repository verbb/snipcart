<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\services;

use workingconcept\snipcart\Snipcart;
use workingconcept\snipcart\models\Customer;
use workingconcept\snipcart\models\Order;
use workingconcept\snipcart\helpers\ModelHelper;

/**
 * Class Customers
 *
 * For interacting with Snipcart customers.
 *
 * @package workingconcept\snipcart\services
 */
class Customers extends \craft\base\Component
{
    // Public Methods
    // =========================================================================

    /**
     * Lists Snipcart customers.
     *
     * @param int    $page   Page of results
     * @param int    $limit  Number of results per page
     * @param array  $params Parameters to send with the request
     *
     * @return \stdClass|array|null
     *              ->totalItems (int)
     *              ->offset (int)
     *              ->limit (int)
     *              ->items (Customer[])
     * @throws \Exception if our API key is missing.
     */
    public function listCustomers($page = 1, $limit = 20, $params = [])
    {
        $params['offset'] = ($page - 1) * $limit;
        $params['limit']  = $limit;

        $customerData = Snipcart::$plugin->api->get('customers', $params);

        $customerData->items = ModelHelper::populateArrayWithModels(
            (array)$customerData->items,
            Customer::class
        );

        return $customerData;
    }

    /**
     * Gets a Snipcart customer.
     *
     * @param string $customerId Snipcart customer ID
     *
     * @return Customer|null
     * @throws \Exception if our API key is missing.
     */
    public function getCustomer($customerId)
    {
        if ($customerData = Snipcart::$plugin->api->get(sprintf(
            'customers/%s',
            $customerId
        )))
        {
            return new Customer((array)$customerData);
        }

        return null;
    }

    /**
     * Gets a customer's order history.
     *
     * @param string $customerId Snipcart customer ID
     *
     * @return Order[]
     * @throws \Exception if our API key is missing.
     */
    public function getCustomerOrders($customerId): array
    {
        $orders = ModelHelper::populateArrayWithModels(
            (array)Snipcart::$plugin->api->get(sprintf(
                'customers/%s/orders',
                $customerId
            ), ['orderBy' => 'creationDate']),
            Order::class
        );

        usort($orders, [$this, 'sortOrdersByDateDescending']);

        return $orders;
    }


    // Private Methods
    // =========================================================================

    /**
     * Descending sort method for Customer object `creationDate` property.
     *
     * @param $a
     * @param $b
     * @return bool
     */
    private function sortOrdersByDateDescending($a, $b): bool
    {
        return $a->creationDate->getTimestamp() < $b->creationDate->getTimestamp();
    }
}