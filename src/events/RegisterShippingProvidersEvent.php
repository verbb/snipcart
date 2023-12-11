<?php
namespace verbb\snipcart\events;

use yii\base\Event;

class RegisterShippingProvidersEvent extends Event
{
    // Properties
    // =========================================================================

    public array $shippingProviders = [];
}
