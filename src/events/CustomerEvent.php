<?php
namespace verbb\snipcart\events;

use verbb\snipcart\models\snipcart\Customer;

use yii\base\Event;

class CustomerEvent extends Event
{
    // Properties
    // =========================================================================

    public Customer $customer;
}
