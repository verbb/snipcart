<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\models\snipcart;

use craft\base\Model;

class CustomerStatistics extends Model
{
    /**
     * @var int
     */
    public $ordersCount;

    /**
     * @var float
     */
    public $ordersAmount;

    public function rules(): array
    {
        return [
            [['ordersCount'],
                'number',
                'integerOnly' => true,
            ],
            [['ordersAmount'],
                'number',
                'integerOnly' => false,
            ],
            [['ordersCount', 'ordersAmount'],
                'default',
                'value' => 0,
            ],
        ];
    }
}
