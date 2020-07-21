<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\events;

use yii\base\Event;
use workingconcept\snipcart\base\ShippingProvider;

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
