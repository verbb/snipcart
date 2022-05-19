<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\events;

use fostercommerce\snipcart\base\ShippingProvider;
use yii\base\Event;

/**
 * Register shipping provider event class.
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */
class RegisterShippingProvidersEvent extends Event
{
    /**
     * @var ShippingProvider[]
     */
    public $shippingProviders = [];
}
