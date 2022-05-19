<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\controllers;

use craft\web\Controller;
use yii\web\Response;
use craft\errors\MissingComponentException;
use Craft;
use fostercommerce\snipcart\Snipcart;

class CustomersController extends Controller
{
    public const SEARCH_KEYWORD_PARAM = 'searchKeywords';

    public const SEARCH_KEYWORD_SESSION_KEY = 'snipcartSearchKeywords';

    /**
     * Displays paginated list of customers.
     *
     * @throws
     */
    public function actionIndex(): Response
    {
        $request = Craft::$app->getRequest();
        $searchKeywords = $this->getSearchKeywords();
        $page = $request->getPageNum();

        if (! empty($searchKeywords)) {
            $customers = Snipcart::$plugin->customers->listCustomers($page, 20, [
                'name' => $searchKeywords,
            ]);
        } else {
            $customers = Snipcart::$plugin->customers->listCustomers($page);
        }

        $totalPages = ceil($customers->totalItems / $customers->limit);

        return $this->renderTemplate(
            'snipcart/cp/customers/index',
            [
                'pageNumber' => $page,
                'totalPages' => $totalPages,
                'totalItems' => $customers->totalItems,
                'customers' => $customers->items,
                'keywords' => $searchKeywords,
            ]
        );
    }

    /**
     * Displays customer detail.
     *
     * @throws
     */
    public function actionCustomerDetail(string $customerId): Response
    {
        $customer = Snipcart::$plugin->customers->getCustomer($customerId);
        $customerOrders = Snipcart::$plugin->customers->getCustomerOrders($customerId);

        return $this->renderTemplate(
            'snipcart/cp/customers/detail',
            [
                'customer' => $customer,
                'orders' => $customerOrders,
            ]
        );
    }

    /**
     * Finds search keywords in request or session, storing them in the
     * session in the process. Returns empty string if no keywords are present.
     *
     * @return array|mixed|string
     * @throws MissingComponentException
     */
    private function getSearchKeywords()
    {
        $request = Craft::$app->getRequest();
        $session = Craft::$app->getSession();

        $requestParam = $request->getParam(self::SEARCH_KEYWORD_PARAM);
        $sessionParam = $session->get(self::SEARCH_KEYWORD_SESSION_KEY);

        $keywords = $requestParam ?? $sessionParam ?? '';

        if ($session) {
            $session->set(self::SEARCH_KEYWORD_SESSION_KEY, $keywords);
        }

        return $keywords;
    }
}
