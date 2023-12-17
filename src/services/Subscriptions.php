<?php
namespace verbb\snipcart\services;

use verbb\snipcart\Snipcart;
use verbb\snipcart\helpers\ModelHelper;
use verbb\snipcart\models\snipcart\Subscription;

use craft\base\Component;

use Exception;
use stdClass;

class Subscriptions extends Component
{
    // Public Methods
    // =========================================================================

    public function listSubscriptions(int $page = 1, int $limit = 20, array $params = []): stdClass
    {
        $params['offset'] = ($page - 1) * $limit;
        $params['limit'] = $limit;

        $response = Snipcart::$plugin->getApi()->get('subscriptions', $params);

        $items = $response->items ?? [];

        return (object) [
            'items' => ModelHelper::safePopulateArrayWithModels($items, Subscription::class),
            'totalItems' => $response->totalItems,
            'offset' => $response->offset,
            'limit' => $limit,
        ];
    }

    public function getSubscription(string $subscriptionId): ?Subscription
    {
        if ($subscriptionData = Snipcart::$plugin->getApi()->get("subscriptions/$subscriptionId")) {
            return ModelHelper::safePopulateModel((array)$subscriptionData, Subscription::class);
        }

        return null;
    }

    public function getSubscriptionInvoices($subscriptionId): array
    {
        $response = Snipcart::$plugin->getApi()->get("subscriptions/$subscriptionId/invoices");

        return is_array($response) ? $response : [];
    }

    public function cancel(string $subscriptionId): mixed
    {
        return Snipcart::$plugin->getApi()->delete("subscriptions/$subscriptionId");
    }

    public function pause(string $subscriptionId): mixed
    {
        return Snipcart::$plugin->getApi()->post("subscriptions/$subscriptionId/pause");
    }

    public function resume(string $subscriptionId): mixed
    {
        return Snipcart::$plugin->getApi()->post("subscriptions/$subscriptionId/resume");
    }
}
