<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\services;

use workingconcept\snipcart\Snipcart;
use workingconcept\snipcart\helpers\ModelHelper;
use workingconcept\snipcart\models\Subscription;

/**
 * Class Subscriptions
 *
 * For interacting with Snipcart subscriptions.
 *
 * @package workingconcept\snipcart\services
 */
class Subscriptions extends \craft\base\Component
{
    // Constants
    // =========================================================================

    // Public Methods
    // =========================================================================

    /**
     * List subscriptions.
     *
     * @return \stdClass|array|null
     * @throws \Exception  Thrown when there isn't an API key to authenticate requests.
     */
    public function listSubscriptions()
    {
        $subscriptionData = Snipcart::$plugin->api->get('subscriptions');

        $subscriptionData->items = ModelHelper::populateArrayWithModels(
            (array)$subscriptionData->items,
            Subscription::class
        );

        return $subscriptionData;
    }

    // Private Methods
    // =========================================================================

}