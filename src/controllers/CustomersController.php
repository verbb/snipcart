<?php
namespace verbb\snipcart\controllers;

use verbb\snipcart\Snipcart;

use Craft;
use craft\web\Controller;

use yii\web\Response;

class CustomersController extends Controller
{
    // Constants
    // =========================================================================

    public const SEARCH_KEYWORD_PARAM = 'searchKeywords';
    public const SEARCH_KEYWORD_SESSION_KEY = 'snipcartSearchKeywords';


    // Public Methods
    // =========================================================================

    public function actionIndex(): Response
    {
        $request = Craft::$app->getRequest();
        $searchKeywords = $this->getSearchKeywords();
        $page = $request->getPageNum();

        if (!empty($searchKeywords)) {
            $customers = Snipcart::$plugin->getCustomers()->listCustomers($page, 20, [
                'name' => $searchKeywords,
            ]);
        } else {
            $customers = Snipcart::$plugin->getCustomers()->listCustomers($page);
        }

        $totalPages = ceil($customers->totalItems / $customers->limit);

        return $this->renderTemplate('snipcart/cp/customers/index', [
            'pageNumber' => $page,
            'totalPages' => $totalPages,
            'totalItems' => $customers->totalItems,
            'customers' => $customers->items,
            'keywords' => $searchKeywords,
        ]);
    }

    public function actionCustomerDetail(string $customerId): Response
    {
        $customer = Snipcart::$plugin->getCustomers()->getCustomer($customerId);
        $customerOrders = Snipcart::$plugin->getCustomers()->getCustomerOrders($customerId);

        return $this->renderTemplate('snipcart/cp/customers/detail', [
            'customer' => $customer,
            'orders' => $customerOrders,
        ]);
    }


    // Private Methods
    // =========================================================================

    private function getSearchKeywords(): mixed
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
