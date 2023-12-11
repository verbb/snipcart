<?php
namespace verbb\snipcart\events;

use verbb\snipcart\models\snipcart\Order;
use verbb\snipcart\models\snipcart\Package;

use craft\events\CancelableEvent;

class ShippingRateEvent extends CancelableEvent
{
    // Properties
    // =========================================================================

    public Order $order;
    public array $rates = [];
    public Package $package;
    public array $errors = [];


    // Public Methods
    // =========================================================================

    public function getErrors(): ?array
    {
        if ($this->isValid) {
            return null;
        }

        $errors = $this->errors ?? [];

        if (empty($errors)) {
            $errors[] = [
                'key' => 'unknown',
                'message' => 'There was an error fetching the shipping rates.',
            ];
        }

        return $errors;
    }
}
