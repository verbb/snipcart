<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\services;

use workingconcept\snipcart\Snipcart;
use workingconcept\snipcart\models\Discount;
use workingconcept\snipcart\helpers\ModelHelper;

/**
 * Class Discounts
 *
 * For interacting with Snipcart discounts.
 *
 * @package workingconcept\snipcart\services
 */
class Discounts extends \craft\base\Component
{
    // Constants
    // =========================================================================

    // Public Methods
    // =========================================================================

    /**
     * List discounts.
     *
     * @return Discount[]
     * @throws \Exception  Thrown when there isn't an API key to authenticate requests.
     */
    public function listDiscounts(): array
    {
        return ModelHelper::populateArrayWithModels(
            (array)Snipcart::$plugin->api->get('discounts'),
            Discount::class
        );
    }

    // Private Methods
    // =========================================================================

}