<?php
namespace verbb\snipcart\events;

use verbb\snipcart\models\snipcart\Order;
use verbb\snipcart\models\snipcart\ShippingRate;
use verbb\snipcart\models\snipcart\Package;
use craft\events\CancelableEvent;

/**
 * Shipping rate event class.
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */
class ShippingRateEvent extends CancelableEvent
{
    /**
     * @var Order
     */
    public $order;

    /**
     * @var ShippingRate[]
     */
    public $rates;

    /**
     * @var Package
     */
    public $package;

    /**
     * @var array[] with 'key' and 'message' keys
     */
    public $errors;

    public function getErrors()
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
