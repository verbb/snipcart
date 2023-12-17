<?php
namespace verbb\snipcart\services;

use verbb\snipcart\helpers\ModelHelper;
use verbb\snipcart\models\snipcart\AbandonedCart;
use verbb\snipcart\Snipcart;

use craft\base\Component;

use Exception;
use stdClass;

class Carts extends Component
{
    // Public Methods
    // =========================================================================

    public function listAbandonedCarts(int $page = 1, int $limit = 20, array $params = []): stdClass
    {
        $params['offset'] = ($page - 1) * $limit;
        $params['limit'] = $limit;

        $response = Snipcart::$plugin->getApi()->get('carts/abandoned', $params);

        return (object) [
            'items' => ModelHelper::safePopulateArrayWithModels($response->items, AbandonedCart::class),
            'continuationToken' => $response->continuationToken ?? null,
            'hasMoreResults' => $response->hasMoreResults ?? false,
            'limit' => $limit,
        ];
    }

    public function getAbandonedCart(string $cartId): ?AbandonedCart
    {
        if ($abandonedCartData = Snipcart::$plugin->getApi()->get("carts/abandoned/$cartId")) {
            return ModelHelper::safePopulateModel((array)$abandonedCartData, AbandonedCart::class);
        }

        return null;
    }
}
