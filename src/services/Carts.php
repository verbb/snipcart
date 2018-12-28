<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\services;

use workingconcept\snipcart\Snipcart;
use workingconcept\snipcart\models\AbandonedCart;
use workingconcept\snipcart\helpers\ModelHelper;

/**
 * Class Carts
 *
 * For interacting with Snipcart carts.
 *
 * @package workingconcept\snipcart\services
 */
class Carts extends \craft\base\Component
{
    // Constants
    // =========================================================================

    // Public Methods
    // =========================================================================

    /**
     * List abandoned carts.
     *
     * @return AbandonedCart[]
     * @throws \Exception if our API key is missing.
     */
    public function listAbandonedCarts()
    {
        $abandonedCartData = Snipcart::$plugin->api->get('carts/abandoned');

        $abandonedCartData->items = ModelHelper::populateArrayWithModels(
            (array)$abandonedCartData->items,
            AbandonedCart::class
        );

        return $abandonedCartData;
    }

    /**
     * Get an abandoned cart.
     * 
     * @param string $token
     *
     * @return AbandonedCart|null
     * @throws \Exception if our API key is missing.
     */
    public function getAbandonedCart($token)
    {
        if ($abandonedCartData = Snipcart::$plugin->api->get(sprintf(
            'carts/abandoned/%s',
            $token
        )))
        {
            return new AbandonedCart((array)$abandonedCartData);
        }

        return null;
    }

    // Private Methods
    // =========================================================================

}