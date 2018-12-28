<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\events;

use workingconcept\snipcart\models\Customer;
use yii\base\Event;

/**
 * Order event class.
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */
class CustomerEvent extends Event
{
    // Properties
    // =========================================================================

    /**
     * @var Customer
     */
    public $customer;

}
