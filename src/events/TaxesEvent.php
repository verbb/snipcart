<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\events;

use workingconcept\snipcart\models\Order;
use workingconcept\snipcart\models\Tax;
use yii\base\Event;

/**
 * Taxes event class.
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */
class TaxesEvent extends Event
{
    // Properties
    // =========================================================================

    /**
     * @var Order
     */
    public $order;

    /**
     * @var Tax[]
     */
    public $taxes;

}
