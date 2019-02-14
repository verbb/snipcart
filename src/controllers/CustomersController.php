<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\controllers;

use workingconcept\snipcart\Snipcart;
use Craft;

class CustomersController extends \craft\web\Controller
{
    // Constants
    // =========================================================================

    const SEARCH_KEYWORD_PARAM = 'searchKeywords';
    const SEARCH_KEYWORD_SESSION_KEY = 'snipcartSearchKeywords';


    // Public Methods
    // =========================================================================

    /**
     * Display paginated list of customers.
     * @return \yii\web\Response
     * @throws
     */
    public function actionIndex(): \yii\web\Response
    {
        $request        = Craft::$app->getRequest();
        $searchKeywords = $this->_getSearchKeywords();
        $page           = $request->getPageNum();

        if ( ! empty($searchKeywords))
        {
            $customers = Snipcart::$plugin->customers->listCustomers($page, 20, [
                'name' => $searchKeywords
            ]);
        }
        else
        {
            $customers = Snipcart::$plugin->customers->listCustomers($page);
        }

        $totalPages = ceil($customers->totalItems / $customers->limit);

        return $this->renderTemplate('snipcart/cp/customers/index',
            [
                'pageNumber' => $page,
                'totalPages' => $totalPages,
                'customers'  => $customers->items,
                'keywords'   => $searchKeywords,
            ]
        );
    }

    /**
     * Display customer detail.
     * @param string $customerId
     * @return \yii\web\Response
     * @throws
     */
    public function actionCustomerDetail(string $customerId): \yii\web\Response
    {
        $customer = Snipcart::$plugin->customers->getCustomer($customerId);
        $customerOrders = Snipcart::$plugin->customers->getCustomerOrders($customerId);

        return $this->renderTemplate('snipcart/cp/customers/detail',
            [
                'customer' => $customer,
                'orders'   => $customerOrders,
            ]
        );
    }


    // Private Methods
    // =========================================================================

    private function _getSearchKeywords()
    {
        $request = Craft::$app->getRequest();
        $session = Craft::$app->getSession();

        $requestParam = $request->getParam(self::SEARCH_KEYWORD_PARAM);
        $sessionParam = $session->get(self::SEARCH_KEYWORD_SESSION_KEY);

        $keywords = $requestParam ?? $sessionParam ?? '';

        if ($session)
        {
            $session->set(self::SEARCH_KEYWORD_SESSION_KEY, $keywords);
        }

        return $keywords;
    }

}