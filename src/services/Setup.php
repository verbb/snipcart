<?php
namespace verbb\snipcart\services;

use verbb\snipcart\Snipcart;
use verbb\snipcart\controllers\WebhooksController;
use verbb\snipcart\fields\ProductDetails;
use verbb\snipcart\records\WebhookLog;

use Craft;
use craft\base\Component;
use craft\base\FieldInterface;

use Throwable;

class Setup extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * The Overview's setup steps, or null once the store has its first order.
     *
     * Each step ticks from real data. The API keys tick once Snipcart answers an order lookup with them,
     * so keys it rejects stay unticked. Setup only ends while the keys work, because the dashboard needs them.
     * An order in the API or a logged order webhook counts, so a test order still counts after switching to live keys.
     */
    public function getSetupSteps(): ?array
    {
        $settings = Snipcart::$plugin->getSettings();
        $keys = false;
        $keysFailed = false;

        if ($settings->isConfigured()) {
            try {
                // Skip the response cache so setup ends as soon as the first order is placed.
                $orders = Snipcart::$plugin->getOrders()->listOrders(1, 1, ['cache' => false]);
                $keys = true;

                if ($orders->totalItems > 0 || $this->_hasCompletedOrderWebhook()) {
                    return null;
                }
            } catch (Throwable $e) {
                // The API throws for rejected keys and fails on an unreachable or unexpected response.
                // Either way the keys aren't working yet, which the step says rather than erroring the page.
                $keysFailed = true;

                Snipcart::error('Could not check Snipcart orders for setup: {message}', [
                    'message' => $e->getMessage(),
                ]);
            }
        }

        $field = $this->_getProductDetailsField();

        return [
            'keys' => $keys,
            'keysFailed' => $keysFailed,
            'testMode' => $settings->testMode,
            'field' => $field,
            'webhook' => WebhookLog::find()->exists(),
        ];
    }


    // Private Methods
    // =========================================================================

    private function _hasCompletedOrderWebhook(): bool
    {
        return WebhookLog::find()
            ->where(['type' => WebhooksController::WEBHOOK_ORDER_COMPLETED])
            ->exists();
    }

    private function _getProductDetailsField(): ?FieldInterface
    {
        $fields = Craft::$app->getFields()->getFieldsByType(ProductDetails::class);

        return $fields[0] ?? null;
    }
}
