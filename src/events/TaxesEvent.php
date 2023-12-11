<?php
namespace verbb\snipcart\events;

use verbb\snipcart\models\snipcart\Order;
use verbb\snipcart\models\snipcart\Tax;
use yii\base\Event;

/**
 * Taxes event class.
 *
 * @link      https://workingconcept.com
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
    public $taxes;

}
