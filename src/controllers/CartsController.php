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
                'pageNumber' => $page,
                'totalPages' => $totalPages,
                'carts'      => $carts->items,
            ]
        );
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