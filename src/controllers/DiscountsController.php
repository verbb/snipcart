<?php
namespace verbb\snipcart\controllers;

use verbb\snipcart\Snipcart;
use verbb\snipcart\models\snipcart\Discount;
use verbb\snipcart\services\Api;

use Craft;
use craft\helpers\UrlHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class DiscountsController extends BaseCpController
{
    // Constants
    // =========================================================================

    private const CREATE_ATTRIBUTES = [
        'name',
        'expires',
        'maxNumberOfUsages',
        'trigger',
        'code',
        'itemId',
        'totalToReach',
        'type',
        'amount',
        'productIds',
        'rate',
        'alternatePrice',
        'shippingDescription',
        'shippingCost',
        'shippingGuaranteedDaysToDelivery',
    ];


    // Properties
    // =========================================================================

    protected array $actionPermissions = [
        'new' => Snipcart::PERMISSION_MANAGE_DISCOUNTS,
        'save' => Snipcart::PERMISSION_MANAGE_DISCOUNTS,
        'update-discount' => Snipcart::PERMISSION_MANAGE_DISCOUNTS,
        'delete-discount' => Snipcart::PERMISSION_MANAGE_DISCOUNTS,
    ];


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

        $params = array_intersect_key(
            Craft::$app->getRequest()->post(),
            array_flip(self::CREATE_ATTRIBUTES),
        );

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
            Api::invalidateCache();
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

        $discountId = (string)Craft::$app->getRequest()->getRequiredBodyParam('discountId');
        $discount = Snipcart::$plugin->getDiscounts()->getDiscount($discountId, false);

        if (!$discount || !$discount->id) {
            throw new NotFoundHttpException('Discount not found.');
        }

        // Successful responses are empty, so there is no response body to check.
        Snipcart::$plugin->getDiscounts()->deleteDiscountById($discount->id);

        Craft::$app->getSession()->setNotice('Discount deleted.');

        Api::invalidateCache();

        return $this->redirect(UrlHelper::cpUrl('snipcart/discounts'));
    }
}
