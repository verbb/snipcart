<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2020 Working Concept Inc.
 */

namespace workingconcept\snipcart\events;

use workingconcept\snipcart\models\snipcart\Refund;
use yii\base\Event;

/**
 * Order refund event class.
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2020 Working Concept Inc.
 */
class OrderRefundEvent extends Event
{
    /**
     * @var Refund
     */
    public $refund;
}
