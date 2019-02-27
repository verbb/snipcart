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
    // Public Methods
    // =========================================================================

    /**
     * Lists abandoned carts.
     *
     * Note that Snipcart's API is weird here, and `totalItems`+`offset` don't
     * behave like pagination anywhere else. Instead we have to use
     * `hasMoreResults` and `continuationToken` provided in the response and
     * disregard `totalItems`+`offset`.
     *
     * @param int   $page   Page of results (starting at 1)
     * @param int   $limit  Results per page
     * @param array $params Parameters to be sent ($page and $limit will be
     *                      overwritten, so set those in this method and not
     *                      as arguments here)
     *
     * @return \stdClass
     *              ->items (AbandonedCart[])
     *              ->totalItems (int)
     *              ->offset (int)
     *              ->limit (int)
     * @throws \Exception if our API key is missing.
     */
    public function listAbandonedCarts($page = 1, $limit = 20, $params = []): \stdClass
    {
        /**
         * Define offset and limit since that's pretty much all we're doing here.
         */
        $params['offset'] = ($page - 1) * $limit;
        $params['limit']  = $limit;

        $response = Snipcart::$plugin->api->get(
            'carts/abandoned',
            $params
        );

        return (object) [
            'items' => ModelHelper::populateArrayWithModels(
                $response->items,
                AbandonedCart::class
            ),
            'totalItems'        => $response->totalItems,
            'offset'            => $response->offset,
            'continuationToken' => $response->continuationToken ?? null,
            'hasMoreResults'    => $response->hasMoreResults ?? false,
            'limit'             => $limit
        ];
    }

    /**
     * Gets an abandoned cart.
     * 
     * @param string $cartId
     *
     * @return AbandonedCart|null
     * @throws \Exception if our API key is missing.
     */
    public function getAbandonedCart($cartId)
    {
        if ($abandonedCartData = Snipcart::$plugin->api->get(sprintf(
            'carts/abandoned/%s',
            $cartId
        )))
        {
            return new AbandonedCart((array)$abandonedCartData);
        }

        return null;
    }
    
}