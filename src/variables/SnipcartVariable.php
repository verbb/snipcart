<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\variables;

use Craft;
use craft\helpers\UrlHelper;
use workingconcept\snipcart\Snipcart;

class SnipcartVariable
{
    
    public function listOrders($pageNumber = 1)
    {
        return Snipcart::$plugin->snipcart->listOrders($pageNumber);
    }
    
    public function listOrdersByDay($pageNumber = 1)
    {
        return Snipcart::$plugin->snipcart->listOrdersByDay($pageNumber);
    }
    
    public function listCustomers($pageNumber = 1)
    {
        return Snipcart::$plugin->snipcart->listCustomers($pageNumber);
    }
    
    public function listDiscounts()
    {
        return Snipcart::$plugin->snipcart->listDiscounts();
    }
    
    public function listAbandoned()
    {
        return Snipcart::$plugin->snipcart->listAbandoned();
    }

    public function listSubscriptions()
    {
        return Snipcart::$plugin->snipcart->listSubscriptions();
    }

    public function listRefunds()
    {
        return Snipcart::$plugin->snipcart->listRefunds();
    }

    public function getOrder($orderId)
    {
        return Snipcart::$plugin->snipcart->getOrder($orderId);
    }

    public function getOrderNotifications($orderId)
    {
        return Snipcart::$plugin->snipcart->getOrderNotifications($orderId);
    }

    public function getOrderRefunds($orderId)
    {
        return Snipcart::$plugin->snipcart->getOrderRefunds($orderId);
    }

    public function getCustomer($customerId)
    {
        return Snipcart::$plugin->snipcart->getCustomer($customerId);
    }
    
    public function getCustomerOrders($customerId)
    {
        return Snipcart::$plugin->snipcart->getCustomerOrders($customerId);
    }
    
    public function snipcartUrl()
    {
        return Snipcart::$plugin->snipcart->snipcartUrl();
    }

    public function publicApiKey()
    {
        return Snipcart::$plugin->settings->publicApiKey;
    }

    public function startDate()
    {
        return \DateTime::createFromFormat('U', Snipcart::$plugin->snipcart->dateRangeStart());
    }
    
    public function endDate()
    {
        return \DateTime::createFromFormat('U', Snipcart::$plugin->snipcart->dateRangeEnd());
    }
    
    public function searchKeywords()
    {
        return Snipcart::$plugin->snipcart->searchKeywords();
    }

    public function searchCustomers($keywords)
    {
        return Snipcart::$plugin->snipcart->searchCustomers($keywords);
    }

    public function isLinked()
    {
        return Snipcart::$plugin->snipcart->isLinked();
    }

}
