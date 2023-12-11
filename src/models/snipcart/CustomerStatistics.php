<?php
namespace verbb\snipcart\models\snipcart;

use craft\base\Model;

class CustomerStatistics extends Model
{
    // Properties
    // =========================================================================

    public ?int $ordersCount = null;;
    public ?float $ordersAmount = null;;


    // Public Methods
    // =========================================================================

    public function rules(): array
    {
        return [
            [['ordersCount'], 'number', 'integerOnly' => true],
            [['ordersAmount'], 'number', 'integerOnly' => false],
            [['ordersCount', 'ordersAmount'], 'default', 'value' => 0],
        ];
    }
}
