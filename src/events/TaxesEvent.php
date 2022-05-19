<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\events;

use fostercommerce\snipcart\models\snipcart\Order;
use fostercommerce\snipcart\models\snipcart\Tax;
use yii\base\Event;

/**
 * Taxes event class.
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */
class TaxesEvent extends Event
{
    /**
     * @var Order
     */
    public $order;

    /**
     * @var Tax[]
     */
    public $taxes = [];
}
