<?php
namespace verbb\snipcart\models\snipcart;

use craft\base\Model;

class CustomerStatistics extends Model
{
    // Properties
    // =========================================================================

    public ?int $ordersCount = 0;
    public ?float $ordersAmount = 0;
    

    // Protected Methods
    // =========================================================================

    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        
        $rules[] = [['ordersCount'], 'number', 'integerOnly' => true];
        $rules[] = [['ordersAmount'], 'number', 'integerOnly' => false];

        return $rules;
    }
}
