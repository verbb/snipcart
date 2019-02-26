<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\controllers;

use workingconcept\snipcart\Snipcart;
use craft\helpers\UrlHelper;
use craft\helpers\DateTimeHelper;
use Craft;

class CartsController extends \craft\web\Controller
{
    // Public Methods
    // =========================================================================

    /**
     * Display paginated list of abandoned carts.
     * @return \yii\web\Response
     * @throws
     */
    public function actionIndex(): \yii\web\Response
    {
        $page       = Craft::$app->getRequest()->getPageNum();
        $carts      = Snipcart::$plugin->carts->listAbandonedCarts($page);
        $totalPages = ceil($carts->totalItems / $carts->limit);

        return $this->renderTemplate('snipcart/cp/abandoned-carts/index',
            [
                'pageNumber'        => $page,
                'totalPages'        => $totalPages,
                'carts'             => $carts->items,
                'continuationToken' => $carts->continuationToken ?? null,
                'hasMoreResults'    => $carts->hasMoreResults ?? false,
            ]
        );
    }

    /**
     * Get the next page/grouping of abandoned carts.
     * @return \yii\web\Response
     * @throws
     */
    public function actionGetNextCarts()
    {
        $this->requirePostRequest();

        $token = Craft::$app->getRequest()->getRequiredParam('continuationToken');

        $response = Snipcart::$plugin->api->get('carts/abandoned', [
            'continuationToken' => $token
        ]);

        if (isset($response->items))
        {
            foreach ($response->items as &$item)
            {
                $item->total = Craft::$app->getFormatter()->asCurrency($item->total);
                $item->cpUrl = UrlHelper::cpUrl('snipcart/abandoned/' . $item->token);

                $date = DateTimeHelper::toDateTime($item->modificationDate);

                $item->modificationDate = $date->format('M j, Y');
            }
        }

        return $this->asJson($response);
    }

    /**
     * Display abandoned cart detail.
     * @param string $cartId
     * @return \yii\web\Response
     * @throws \Exception
     */
    public function actionDetail(string $cartId): \yii\web\Response
    {
        $abandonedCart = Snipcart::$plugin->carts->getAbandonedCart($cartId);

        return $this->renderTemplate('snipcart/cp/abandoned-carts/detail',
            [
                'abandonedCart' => $abandonedCart,
            ]
        );
    }

}