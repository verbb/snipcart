<?php
namespace verbb\snipcart\events;

use yii\base\Event;
use verbb\snipcart\base\ShippingProvider;

/**
 * Register shipping provider event class.
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */
class RegisterShippingProvidersEvent extends Event
{
    /**
     * @var ShippingProvider[]
     */
    public $shippingProviders;

}
