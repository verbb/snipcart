<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\variables;

use workingconcept\snipcart\models\Customer;
use workingconcept\snipcart\models\Order;
use workingconcept\snipcart\Snipcart;

class SnipcartVariable
{

    /**
     * @param int $pageNumber
     * @return \stdClass|array
     * @throws \Exception
     */
    public function listOrders($pageNumber = 1)
    {
        return Snipcart::$plugin->orders->listOrders($pageNumber);
    }

    /**
     * @param int $pageNumber
     * @return array
     * @throws \Exception
     */
    public function listOrdersByDay($pageNumber = 1): array
    {
        return Snipcart::$plugin->orders->listOrdersByDay($pageNumber);
    }

    /**
     * @param int $pageNumber
     * @return \stdClass|array|null
     * @throws \Exception
     */
    public function listCustomers($pageNumber = 1)
    {
        return Snipcart::$plugin->customers->listCustomers($pageNumber);
    }

    /**
     * @return \stdClass|array
     * @throws \Exception
     */
    public function listDiscounts()
    {
        return Snipcart::$plugin->discounts->listDiscounts();
    }

    /**
     * @return \stdClass|array
     * @throws \Exception
     */
    public function listAbandonedCarts()
    {
        return Snipcart::$plugin->carts->listAbandonedCarts();
    }

    /**
     * @return \stdClass|array
     * @throws \Exception
     */
    public function listSubscriptions()
    {
        return Snipcart::$plugin->subscriptions->listSubscriptions();
    }

    /**
     * @param $orderId
     * @return Order|null
     * @throws \Exception
     */
    public function getOrder($orderId)
    {
        return Snipcart::$plugin->orders->getOrder($orderId);
    }

    /**
     * @param $orderId
     * @return \stdClass|array
     * @throws \Exception
     */
    public function getOrderNotifications($orderId)
    {
        return Snipcart::$plugin->orders->getOrderNotifications($orderId);
    }

    /**
     * @param $orderId
     * @return \stdClass|array
     * @throws \Exception
     */
    public function getOrderRefunds($orderId)
    {
        return Snipcart::$plugin->orders->getOrderRefunds($orderId);
    }

    /**
     * @param $customerId
     * @return Customer|null
     * @throws \Exception
     */
    public function getCustomer($customerId)
    {
        return Snipcart::$plugin->customers->getCustomer($customerId);
    }

    /**
     * @param $customerId
     * @return \stdClass|array
     * @throws \Exception
     */
    public function getCustomerOrders($customerId)
    {
        return Snipcart::$plugin->customers->getCustomerOrders($customerId);
    }

    /**
     * @return string
     */
    public function publicApiKey(): string
    {
        return Snipcart::$plugin->getSettings()->publicApiKey;
    }

    /**
     * @return bool|\DateTime
     * @throws
     */
    public function startDate()
    {
        return \DateTime::createFromFormat('U', Snipcart::$plugin->orders->dateRangeStart());
    }

    /**
     * @return bool|\DateTime
     * @throws
     */
    public function endDate()
    {
        return \DateTime::createFromFormat('U', Snipcart::$plugin->orders->dateRangeEnd());
    }

    /**
     * @return mixed|string
     * @throws
     */
    public function searchKeywords()
    {
        return Snipcart::$plugin->orders->searchKeywords();
    }

    /**
     * @param $keywords
     * @return \stdClass|array|null
     * @throws \Exception
     */
    public function searchCustomers($keywords)
    {
        return Snipcart::$plugin->customers->searchCustomers($keywords);
    }

    /**
     * @return bool
     */
    public function isLinked(): bool
    {
        return Snipcart::$plugin->api->isLinked;
    }

}
