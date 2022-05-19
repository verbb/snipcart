<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\controllers;

use fostercommerce\snipcart\Snipcart;
use craft\helpers\UrlHelper;
use craft\helpers\DateTimeHelper;
use Craft;
use yii\web\Response;

class CartsController extends \craft\web\Controller
{
    /**
     * Displays paginated list of abandoned carts.
     *
     * @return Response
     * @throws
     */
    public function actionIndex(): Response
    {
        $page  = Craft::$app->getRequest()->getPageNum();
        $carts = Snipcart::$plugin->carts->listAbandonedCarts($page);

        return $this->renderTemplate(
            'snipcart/cp/abandoned-carts/index',
            [
                'pageNumber'        => $page,
                'carts'             => $carts->items,
                'continuationToken' => $carts->continuationToken ?? null,
                'hasMoreResults'    => $carts->hasMoreResults ?? false,
            ]
        );
    }

    /**
     * Gets the next page/grouping of abandoned carts.
     *
     * @return Response
     * @throws
     */
    public function actionGetNextCarts(): Response
    {
        $this->requirePostRequest();

        $token = Craft::$app->getRequest()->getRequiredParam('continuationToken');

        $response = Snipcart::$plugin->api->get('carts/abandoned', [
            'continuationToken' => $token
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

    /**
     * Displays abandoned cart detail.
     *
     * @param string $cartId
     * @return Response
     * @throws \Exception
     */
    public function actionDetail(string $cartId): Response
    {
        $abandonedCart = Snipcart::$plugin->carts->getAbandonedCart($cartId);

        return $this->renderTemplate(
            'snipcart/cp/abandoned-carts/detail',
            [
                'abandonedCart' => $abandonedCart,
            ]
        );
    }

}