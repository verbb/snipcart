<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\events;

use fostercommerce\snipcart\models\snipcart\Order;
use yii\base\Event;

/**
 * Order event class.
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */
class OrderEvent extends Event
{
    /**
     * @var Order
     */
    public $order;
}
