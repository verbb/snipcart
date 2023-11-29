<?php

namespace fostercommerce\snipcart\errors;

use fostercommerce\snipcart\events\ShippingRateEvent;

class ShippingRateException extends \Exception
{
    /**
     * @var ShippingRateEvent The event that was marked as invalid
     */
    public $event;

    public function __construct(ShippingRateEvent $shippingRateEvent, string $message = null, int $code = 0, \Throwable $throwable = null)
    {
        $this->event = $shippingRateEvent;

        if ($message === null) {
            $message = 'An error occurred while fetching the shipping rates for order "' . ($shippingRateEvent->order->invoiceNumber ?? $shippingRateEvent->order->token) . '": ' . implode(', ', array_column($shippingRateEvent->getErrors(), 'message'));
        }

        parent::__construct($message, $code, $throwable);
    }
}
