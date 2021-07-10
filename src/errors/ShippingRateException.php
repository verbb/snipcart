<?php

namespace fostercommerce\snipcart\errors;

use fostercommerce\snipcart\events\ShippingRateEvent;

class ShippingRateException extends \Exception
{
    /**
     * @var ShippingRateEvent The event that was marked as invalid
     */
    public $event;

    public function __construct(ShippingRateEvent $event, string $message = null, int $code = 0, \Throwable $previous = null) {
        $this->event = $event;

        if ($message === null) {
            $message = 'An error occurred while fetching the shipping rates for order "' . ($event->order->invoiceNumber ?? $event->order->token) . '": ' . implode(', ', array_column($event->getErrors(), 'message'));
        }

        parent::__construct($message, $code, $previous);
    }
}
