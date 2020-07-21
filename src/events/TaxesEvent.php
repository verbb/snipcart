<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\events;

use workingconcept\snipcart\models\snipcart\Order;
use workingconcept\snipcart\models\snipcart\Tax;
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
