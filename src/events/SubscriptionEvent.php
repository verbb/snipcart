<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\events;

use workingconcept\snipcart\models\snipcart\Subscription;
use yii\base\Event;

/**
 * Subscription event class.
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */
class SubscriptionEvent extends Event
{
    /**
     * @var Subscription
     */
    public $subscription;

}
