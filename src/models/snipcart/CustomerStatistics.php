<?php
namespace verbb\snipcart\models\snipcart;

class CustomerStatistics extends \craft\base\Model
{
    /**
     * @var int
     */
    public $ordersCount;

    /**
     * @var float
     */
    public $ordersAmount;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['ordersCount'], 'number', 'integerOnly' => true],
            [['ordersAmount'], 'number', 'integerOnly' => false],
            [['ordersCount', 'ordersAmount'], 'default', 'value' => 0],
        ];
    }
}
