<?php
namespace verbb\snipcart\errors;

use verbb\snipcart\events\ShippingRateEvent;

use Exception;
use Throwable;

class ShippingRateException extends Exception
{
    // Properties
    // =========================================================================

    public ShippingRateEvent $event;


    // Public Properties
    // =========================================================================

    public function __construct(ShippingRateEvent $shippingRateEvent, string $message = null, int $code = 0, Throwable $throwable = null)
    {
        $this->event = $shippingRateEvent;

        if ($message === null) {
            $message = 'An error occurred while fetching the shipping rates for order "' . ($shippingRateEvent->order->invoiceNumber ?? $shippingRateEvent->order->token) . '": ' . implode(', ', array_column($shippingRateEvent->getErrors(), 'message'));
        }

        parent::__construct($message, $code, $throwable);
    }
}
