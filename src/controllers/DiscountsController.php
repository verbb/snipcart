<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\controllers;

use workingconcept\snipcart\models\Discount;
use workingconcept\snipcart\Snipcart;
use craft\helpers\UrlHelper;
use Craft;
use yii\web\Response;

class DiscountsController extends \craft\web\Controller
{
    /**
     * @return \yii\web\Response
     * @throws \craft\errors\MissingComponentException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionCreateDiscount(): Response
    {
        $this->requirePostRequest();

        $params = Craft::$app->getRequest()->post();

        unset($params['CRAFT_CSRF_TOKEN'], $params['action']);

        if ( ! $discount = new Discount($params))
        {
            Craft::$app->getUrlManager()->setRouteParams([
                'variables' => ['discount' => $discount]
            ]);
        }
        else
        {
            if ( ! $discount->validate())
            {
                Craft::$app->getUrlManager()->setRouteParams([
                    'variables' => ['discount' => $discount]
                ]);

                Craft::$app->getSession()->setError('Invalid Discount details.');
            }
            else
            {
                if ($result = Snipcart::$plugin->discounts->createDiscount($discount))
                {
                    Craft::$app->getSession()->setNotice('Discount saved.');
                }
                else
                {
                    Craft::$app->getSession()->setError('Failed to save Discount.');
                }
            }
        }

        return $this->redirect(UrlHelper::cpUrl('snipcart/discounts'));
    }

    /**
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionUpdateDiscount()
    {
        $this->requirePostRequest();
    }

    /**
     * @throws \yii\web\BadRequestHttpException
     * @throws \craft\errors\MissingComponentException
     */
    public function actionDeleteDiscount(): Response
    {
        $this->requirePostRequest();

        $discountId = Craft::$app->getRequest()->post('discountId');

        // successful response will be `null`, do don't bother checking
        Snipcart::$plugin->discounts->deleteDiscountById($discountId);

        Craft::$app->getSession()->setNotice('Discount deleted.');

        /**
         * Clear cache so we don't return to see our deleted item on the list.
         * @todo Be more conservative about this, just clearing Snipcart or
         *       even 'discounts' caches.
         */
        $cacheService = Craft::$app->getCache();
        $cacheService->flush();

        return $this->redirect(UrlHelper::cpUrl('snipcart/discounts'));
    }
}