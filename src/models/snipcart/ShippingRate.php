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

    public function rules(): array
    {
        return [
            [['guaranteedDaysToDelivery'], 'number', 'integerOnly' => true],
            [['cost'], 'number', 'integerOnly' => false],
            [['description', 'code'], 'string'],
            [['cost', 'description'], 'required'],
        ];
    }

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
}
