<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\controllers;

use workingconcept\snipcart\models\snipcart\Discount;
use workingconcept\snipcart\Snipcart;
use craft\helpers\UrlHelper;
use Craft;

class DiscountsController extends \craft\web\Controller
{
    /**
     * Displays discounts, which don’t come paginated.
     *
     * @return \yii\web\Response
     * @throws
     */
    public function actionIndex(): \yii\web\Response
    {
        return $this->renderTemplate(
            'snipcart/cp/discounts/index',
            [
                'discounts' => Snipcart::$plugin->discounts->listDiscounts()
            ]
        );
    }

    /**
     * Displays discount detail.
     *
     * @param string $discountId
     * @return \yii\web\Response
     * @throws
     */
    public function actionDiscountDetail(string $discountId): \yii\web\Response
    {
        return $this->renderTemplate(
            'snipcart/cp/discounts/detail',
            [
                'discount' => Snipcart::$plugin->discounts->getDiscount($discountId)
            ]
        );
    }

    /**
     * Displays new discount form.
     *
     * @return \yii\web\Response
     */
    public function actionNew(): \yii\web\Response
    {
        return $this->renderTemplate('snipcart/cp/discounts/new');
    }

    /**
     * Saves a new discount.
     *
     * @return \yii\web\Response
     * @throws \craft\errors\MissingComponentException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionSave(): \yii\web\Response
    {
        $this->requirePostRequest();

        $params = Craft::$app->getRequest()->post();

        unset($params['CRAFT_CSRF_TOKEN'], $params['action']);

        if (! $discount = new Discount($params)) {
            Craft::$app->getUrlManager()->setRouteParams([
                'variables' => ['discount' => $discount]
            ]);
        } elseif (! $discount->validate()) {
            Craft::$app->getUrlManager()->setRouteParams([
                'variables' => ['discount' => $discount]
            ]);

            Craft::$app->getSession()->setError('Invalid Discount details.');
        } elseif (Snipcart::$plugin->discounts->createDiscount($discount)) {
            Craft::$app->getSession()->setNotice('Discount saved.');
        } else {
            Craft::$app->getSession()->setError('Failed to save Discount.');
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
     * Deletes a discount.
     *
     * @throws \yii\web\BadRequestHttpException
     * @throws \craft\errors\MissingComponentException
     */
    public function actionDeleteDiscount(): \yii\web\Response
    {
        $this->requirePostRequest();

        $discountId = (string)Craft::$app->getRequest()->post('discountId');

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