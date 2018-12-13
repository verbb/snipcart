<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\events;

use workingconcept\snipcart\models\SnipcartOrder;
use workingconcept\snipcart\models\SnipcartShippingRate;
use workingconcept\snipcart\models\SnipcartPackage;
use yii\base\Event;

/**
 * Webhook event class.
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */
class WebhookEvent extends Event
{
    // Properties
    // =========================================================================

    /**
     * @var SnipcartOrder
     */
    public $order;

    /**
     * @var SnipcartShippingRate[]
     */
    public $rates;

    /**
     * @var SnipcartPackage[]
     */
    public $packaging;

}
