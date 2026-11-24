<?php
namespace verbb\snipcart\services;

use verbb\snipcart\Snipcart;
use verbb\snipcart\controllers\WebhooksController;
use verbb\snipcart\fields\ProductDetails;
use verbb\snipcart\records\WebhookLog;

use Craft;
use craft\base\Component;
use craft\base\FieldInterface;
use craft\helpers\DateTimeHelper;

use DateTime;
use DateTimeZone;
use Throwable;

class Setup extends Component
{
    // Constants
    // =========================================================================

    /**
     * Installs from this date on get the setup checklist. Sites that installed Snipcart earlier never see it.
     */
    public const SETUP_CHECKLIST_SINCE = '2026-11-23';


    // Public Methods
    // =========================================================================

    /**
     * The Overview's setup steps, or null once the store has its first order or for an install that predates them.
     *
     * Each step ticks from real data. The API keys tick once Snipcart answers an order lookup with them,
     * so keys it rejects stay unticked. Setup only ends while the keys work, because the dashboard needs them.
     * An order in the API or a logged order webhook counts, so a test order still counts after switching to live keys.
     */
    public function getSetupSteps(): ?array
    {
        if (!$this->_isNewInstall()) {
            return null;
        }

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

    private function _isNewInstall(): bool
    {
        // Craft records this per environment on install, so it needs no storage of our own. A missing date counts as an existing install.
        $installDate = Craft::$app->getPlugins()->getStoredPluginInfo('snipcart')['installDate'] ?? null;

        if (!$installDate instanceof DateTime) {
            $installDate = DateTimeHelper::toDateTime($installDate) ?: null;
        }

        if (!$installDate) {
            return false;
        }

        return $installDate >= new DateTime(self::SETUP_CHECKLIST_SINCE, new DateTimeZone('UTC'));
    }

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
