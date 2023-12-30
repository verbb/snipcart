<?php
namespace verbb\snipcart\models\snipcart;

use craft\base\Model;

class ShippingRate extends Model
{
    // Properties
    // =========================================================================

    public ?float $cost = null;
    public ?string $description = null;
    public ?string $code = null;
    public ?int $guaranteedDaysToDelivery = null;


    // Public Methods
    // =========================================================================

    public function fields(): array
    {
        $return = [
            'cost',
            'description',
        ];

        if (!empty($this->code)) {
            $return[] = 'code';
        }

        if (!empty($this->guaranteedDaysToDelivery)) {
            $return[] = 'guaranteedDaysToDelivery';
        }

        return $return;
    }
    

    // Protected Methods
    // =========================================================================

    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        
        $rules[] = [['guaranteedDaysToDelivery'], 'number', 'integerOnly' => true];
        $rules[] = [['cost'], 'number', 'integerOnly' => false];
        $rules[] = [['cost', 'description'], 'required'];

        return $rules;
    }
}
