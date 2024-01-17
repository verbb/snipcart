<?php
namespace verbb\snipcart\models\snipcart;

use craft\base\Model;

use DateTime;

class Product extends Model
{
    // Properties
    // =========================================================================

    public ?string $id = null;
    public ?DateTime $creationDate = null;
    public ?DateTime $modificationDate = null;
    public ?string $mode = null;
    public ?string $userDefinedId = null;
    public ?string $url = null;
    public ?string $price = null;
    public ?string $name = null;
    public ?string $description = null;
    public ?string $image = null;
    public ?string $archived = null;
    public ?string $statistics = null;
    public ?string $customFields = null;
    public stdClass|array|null $metadata = null;
    public ?string $inventoryManagementMethod = null;
    public ?string $stock = null;
    public ?string $totalStock = null;
    public ?string $allowOutOfStockPurchases = null;
    public ?string $variants = null;
}
