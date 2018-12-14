<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\variables;

use workingconcept\snipcart\models\SnipcartOrder;
use workingconcept\snipcart\Snipcart;

class SnipcartVariable
{

    /**
     * @param int $pageNumber
     * @return \stdClass
     * @throws \Exception
     */
    public function listOrders($pageNumber = 1)
    {
        return Snipcart::$plugin->snipcart->listOrders($pageNumber);
    }

    /**
     * @param int $pageNumber
     * @return array
     */
    public function listOrdersByDay($pageNumber = 1): array
    {
        return Snipcart::$plugin->snipcart->listOrdersByDay($pageNumber);
    }

    /**
     * @param int $pageNumber
     * @return array
     * @throws \Exception
     */
    public function listCustomers($pageNumber = 1): array
    {
        return Snipcart::$plugin->snipcart->listCustomers($pageNumber);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function listDiscounts(): array
    {
        return Snipcart::$plugin->snipcart->listDiscounts();
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function listAbandoned(): array
    {
        return Snipcart::$plugin->snipcart->listAbandoned();
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function listSubscriptions(): array
    {
        return Snipcart::$plugin->snipcart->listSubscriptions();
    }

    /**
     * @param $orderId
     * @return SnipcartOrder
     * @throws \Exception
     */
    public function getOrder($orderId): SnipcartOrder
    {
        return Snipcart::$plugin->snipcart->getOrder($orderId);
    }

    /**
     * @param $orderId
     * @return \stdClass
     * @throws \yii\base\Exception
     */
    public function getOrderNotifications($orderId)
    {
        return Snipcart::$plugin->snipcart->getOrderNotifications($orderId);
    }

    /**
     * @param $orderId
     * @return \stdClass
     * @throws \yii\base\Exception
     */
    public function getOrderRefunds($orderId)
    {
        return Snipcart::$plugin->snipcart->getOrderRefunds($orderId);
    }

    /**
     * @param $customerId
     * @return \stdClass
     * @throws \Exception
     */
    public function getCustomer($customerId)
    {
        return Snipcart::$plugin->snipcart->getCustomer($customerId);
    }

    /**
     * @param $customerId
     * @return \stdClass
     * @throws \Exception
     */
    public function getCustomerOrders($customerId)
    {
        return Snipcart::$plugin->snipcart->getCustomerOrders($customerId);
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
     */
    public function startDate()
    {
        return \DateTime::createFromFormat('U', Snipcart::$plugin->snipcart->dateRangeStart());
    }

    /**
     * @return bool|\DateTime
     */
    public function endDate()
    {
        return \DateTime::createFromFormat('U', Snipcart::$plugin->snipcart->dateRangeEnd());
    }

    /**
     * @return mixed|string
     */
    public function searchKeywords()
    {
        return Snipcart::$plugin->snipcart->searchKeywords();
    }

    /**
     * @param $keywords
     * @return array
     * @throws \Exception
     */
    public function searchCustomers($keywords): array
    {
        return Snipcart::$plugin->snipcart->searchCustomers($keywords);
    }

    /**
     * @return bool
     */
    public function isLinked(): bool
    {
        return Snipcart::$plugin->snipcart->isLinked();
    }

}
