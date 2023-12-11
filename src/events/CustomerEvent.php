<?php
namespace verbb\snipcart\events;

use verbb\snipcart\models\snipcart\Customer;
use yii\base\Event;

/**
 * Customer event class.
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */
class CustomerEvent extends Event
{
    /**
     * @var Customer
     */
    public $customer;

}
