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
use yii\web\BadRequestHttpException;
use Craft;
use craft\helpers\UrlHelper;
use fostercommerce\snipcart\models\snipcart\Discount;
use fostercommerce\snipcart\Snipcart;

class DiscountsController extends Controller
{
    /**
     * Displays discounts, which don’t come paginated.
     *
     * @throws
     */
    public function actionIndex(): Response
    {
        return $this->renderTemplate(
            'snipcart/cp/discounts/index',
            [
                'discounts' => Snipcart::$plugin->discounts->listDiscounts(),
            ]
        );
    }

    /**
     * Displays discount detail.
     *
     * @throws
     */
    public function actionDiscountDetail(string $discountId): Response
    {
        return $this->renderTemplate(
            'snipcart/cp/discounts/detail',
            [
                'discount' => Snipcart::$plugin->discounts->getDiscount($discountId),
            ]
        );
    }

    /**
     * Displays new discount form.
     */
    public function actionNew(): Response
    {
        return $this->renderTemplate('snipcart/cp/discounts/new');
    }

    /**
     * Saves a new discount.
     *
     * @throws MissingComponentException
     * @throws BadRequestHttpException
     */
    public function actionSave(): Response
    {
        $this->requirePostRequest();

        $params = Craft::$app->getRequest()->post();

        unset($params['CRAFT_CSRF_TOKEN'], $params['action']);

        if (! $discount = new Discount($params)) {
            Craft::$app->getUrlManager()->setRouteParams([
                'variables' => [
                    'discount' => $discount,
                ],
            ]);
        } elseif (! $discount->validate()) {
            Craft::$app->getUrlManager()->setRouteParams([
                'variables' => [
                    'discount' => $discount,
                ],
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
     * @throws BadRequestHttpException
     */
    public function actionUpdateDiscount(): void
    {
        $this->requirePostRequest();
    }

    /**
     * Deletes a discount.
     *
     * @throws BadRequestHttpException
     * @throws MissingComponentException
     */
    public function actionDeleteDiscount(): Response
    {
        $this->requirePostRequest();

        $discountId = (string) Craft::$app->getRequest()->post('discountId');

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
