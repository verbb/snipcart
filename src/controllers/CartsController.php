<?php
namespace verbb\snipcart\controllers;

use verbb\snipcart\Snipcart;

use Craft;
use craft\helpers\DateTimeHelper;
use craft\helpers\UrlHelper;
use craft\web\Controller;

use yii\web\Response;

class CartsController extends Controller
{
    // Public Methods
    // =========================================================================

    public function actionIndex(): Response
    {
        $page = Craft::$app->getRequest()->getPageNum();
        $carts = Snipcart::$plugin->getCarts()->listAbandonedCarts($page);

        return $this->renderTemplate('snipcart/cp/abandoned-carts/index', [
            'pageNumber' => $page,
            'carts' => $carts->items,
            'continuationToken' => $carts->continuationToken ?? null,
            'hasMoreResults' => $carts->hasMoreResults ?? false,
        ]);
    }

    public function actionGetNextCarts(): Response
    {
        $this->requirePostRequest();

        $token = Craft::$app->getRequest()->getRequiredParam('continuationToken');

        $response = Snipcart::$plugin->getApi()->get('carts/abandoned', [
            'continuationToken' => $token,
        ]);

        if (isset($response->items)) {
            foreach ($response->items as &$item) {
                $item->total = Craft::$app->getFormatter()->asCurrency($item->total);
                $item->cpUrl = UrlHelper::cpUrl('snipcart/abandoned/' . $item->token);

                $date = DateTimeHelper::toDateTime($item->modificationDate);

                $item->modificationDate = $date->format('M j, Y');
            }
        }

        return $this->asJson($response);
    }

    public function actionDetail(string $cartId): Response
    {
        $abandonedCart = Snipcart::$plugin->getCarts()->getAbandonedCart($cartId);

        return $this->renderTemplate('snipcart/cp/abandoned-carts/detail', [
            'abandonedCart' => $abandonedCart,
        ]);
    }
}
