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

    /**
     * Get a Snipcart subscription.
     *
     * @param string $subscriptionId Snipcart order GUID
     * @return Subscription|null
     * @throws \Exception if our API key is missing.
     */
    public function getSubscription($subscriptionId)
    {
        if ($subscriptionData = Snipcart::$plugin->api->get(sprintf(
            'subscriptions/%s',
            $subscriptionId
        )))
        {
            return new Subscription((array)$subscriptionData);
        }

        return null;
    }

    /**
     * Cancel a subscription.
     *
     * @param $subscriptionId
     * @return mixed
     * @throws \Exception if our API key is missing.
     */
    public function cancel($subscriptionId)
    {
        return Snipcart::$plugin->api->delete(
            sprintf('subscriptions/%s', $subscriptionId)
        );
    }

    /**
     * Pause a subscription.
     *
     * @param $subscriptionId
     * @return mixed
     * @throws \Exception if our API key is missing.
     */
    public function pause($subscriptionId)
    {
        return Snipcart::$plugin->api->post(
            sprintf('subscriptions/%s/pause', $subscriptionId)
        );
    }

    /**
     * Resume a subscription.
     *
     * @param $subscriptionId
     * @return mixed
     * @throws \Exception if our API key is missing.
     */
    public function resume($subscriptionId)
    {
        return Snipcart::$plugin->api->post(
            sprintf('subscriptions/%s/resume', $subscriptionId)
        );
    }

    // Private Methods
    // =========================================================================

}