<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\events;

use fostercommerce\snipcart\models\snipcart\Order;
use fostercommerce\snipcart\models\snipcart\ShippingRate;
use fostercommerce\snipcart\models\snipcart\Package;
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
