<?php
namespace verbb\snipcart\models\snipcart;

/**
 * https://docs.snipcart.com/v2/api-reference/custom-shipping-methods
 */

class ShippingRate extends \craft\base\Model
{
    /**
     * @var float
     */
    public $cost;
    
    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $code;

    /**
     * @var int
     */
    public $guaranteedDaysToDelivery;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['guaranteedDaysToDelivery'], 'number', 'integerOnly' => true],
            [['cost'], 'number', 'integerOnly' => false],
            [['description', 'code'], 'string'],
            [['cost', 'description'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        $return = [
            'cost',
            'description',
        ];

        if (! empty($this->code)) {
            $return[] = 'code';
        }

        if (! empty($this->guaranteedDaysToDelivery)) {
            $return[] = 'guaranteedDaysToDelivery';
        }

        return $return;
    }
}
