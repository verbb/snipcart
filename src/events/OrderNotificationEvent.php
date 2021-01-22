<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2020 Working Concept Inc.
 */

namespace fostercommerce\snipcart\events;

use fostercommerce\snipcart\models\snipcart\Notification;
use yii\base\Event;

/**
 * Order refund event class.
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2020 Working Concept Inc.
 */
class OrderNotificationEvent extends Event
{

    /**
     * @var Notification
     */
    public $notification;

}
