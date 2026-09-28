<?php
namespace verbb\snipcart\services;

use verbb\snipcart\helpers\ModelHelper;
use verbb\snipcart\models\snipcart\Discount;
use verbb\snipcart\Snipcart;

use craft\base\Component;

use Exception;
use stdClass;

class Discounts extends Component
{
    // Public Methods
    // =========================================================================

    public function listDiscounts(): array
    {
        $response = Snipcart::$plugin->getApi()->get('discounts');

        return ModelHelper::safePopulateArrayWithModels((array)$response, Discount::class);
    }

    public function createDiscount(Discount $discount): mixed
    {
        return Snipcart::$plugin->getApi()->post('discounts', $discount->getPayloadForPost());
    }

    public function getDiscount(string $discountId, bool $useCache = true): ?Discount
    {
        $discountId = rawurlencode($discountId);

        if ($discountData = Snipcart::$plugin->getApi()->get("discounts/$discountId", [], $useCache)) {
            return ModelHelper::safePopulateModel((array)$discountData, Discount::class);
        }

        return null;
    }

    public function updateDiscount(Discount $discount): array|stdClass
    {
        $discountId = rawurlencode((string)$discount->id);

        return Snipcart::$plugin->getApi()->put("discounts/$discountId", $discount->getPayloadForPost(false));
    }

    public function deleteDiscountById(string $discountId): mixed
    {
        $discountId = rawurlencode($discountId);

        return Snipcart::$plugin->getApi()->delete("discounts/$discountId");
    }
}
