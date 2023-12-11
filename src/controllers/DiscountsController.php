<?php
namespace verbb\snipcart\controllers;

use verbb\snipcart\models\snipcart\Discount;
use verbb\snipcart\Snipcart;

use Craft;
use craft\helpers\UrlHelper;
use craft\web\Controller;

use yii\web\Response;

class DiscountsController extends Controller
{
    // Public Methods
    // =========================================================================

    public function actionIndex(): Response
    {
        return $this->renderTemplate('snipcart/cp/discounts/index', [
            'discounts' => Snipcart::$plugin->getDiscounts()->listDiscounts(),
        ]);
    }

    public function actionDiscountDetail(string $discountId): Response
    {
        return $this->renderTemplate('snipcart/cp/discounts/detail', [
            'discount' => Snipcart::$plugin->getDiscounts()->getDiscount($discountId),
        ]);
    }

    public function actionNew(): Response
    {
        return $this->renderTemplate('snipcart/cp/discounts/new');
    }

    public function actionSave(): Response
    {
        $this->requirePostRequest();

        $params = Craft::$app->getRequest()->post();

        unset($params['CRAFT_CSRF_TOKEN'], $params['action']);

        if (!$discount = new Discount($params)) {
            Craft::$app->getUrlManager()->setRouteParams([
                'variables' => [
                    'discount' => $discount,
                ],
            ]);
        } else if (!$discount->validate()) {
            Craft::$app->getUrlManager()->setRouteParams([
                'variables' => [
                    'discount' => $discount,
                ],
            ]);

            Craft::$app->getSession()->setError('Invalid Discount details.');
        } else if (Snipcart::$plugin->getDiscounts()->createDiscount($discount)) {
            Craft::$app->getSession()->setNotice('Discount saved.');
        } else {
            Craft::$app->getSession()->setError('Failed to save Discount.');
        }

        return $this->redirect(UrlHelper::cpUrl('snipcart/discounts'));
    }

    public function actionUpdateDiscount(): void
    {
        $this->requirePostRequest();
    }

    public function actionDeleteDiscount(): Response
    {
        $this->requirePostRequest();

        $discountId = (string) Craft::$app->getRequest()->post('discountId');

        // successful response will be `null`, do don't bother checking
        Snipcart::$plugin->getDiscounts()->deleteDiscountById($discountId);

        Craft::$app->getSession()->setNotice('Discount deleted.');

        // Clear cache so we don't return to see our deleted item on the list.
        // @todo Be more conservative about this, just clearing Snipcart or even 'discounts' caches.
        $cacheService = Craft::$app->getCache();
        $cacheService->flush();

        return $this->redirect(UrlHelper::cpUrl('snipcart/discounts'));
    }
}
