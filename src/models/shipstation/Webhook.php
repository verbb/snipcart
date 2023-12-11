<?php
namespace verbb\snipcart\models\shipstation;

use craft\base\Model;

class Webhook extends Model
{
    // Constants
    // =========================================================================

    public const TYPE_ORDER_NOTIFY = 'ORDER_NOTIFY';
    public const TYPE_ITEM_ORDER_NOTIFY = 'ITEM_ORDER_NOTIFY';
    public const TYPE_SHIP_NOTIFY = 'SHIP_NOTIFY';
    public const TYPE_ITEM_SHIP_NOTIFY = 'ITEM_SHIP_NOTIFY';
    

    // Properties
    // =========================================================================

    public ?string $resource_url = null;
    public ?string $resource_type = null;
    

    // Public Methods
    // =========================================================================

    public function rules(): array
    {
        return [
            [['resource_url', 'resource_type'], 'string', 'max' => 200],
            [['resource_url'], 'url'],
            [['resource_type'], 'in', 'range' => [
                self::TYPE_ORDER_NOTIFY,
                self::TYPE_ITEM_ORDER_NOTIFY,
                self::TYPE_SHIP_NOTIFY,
                self::TYPE_ITEM_SHIP_NOTIFY,
            ]],
        ];
    }
}
