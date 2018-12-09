<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models;

use Craft;
use craft\base\Model;

/**
 * https://docs.snipcart.com/api-reference/notifications
 */

class SnipcartNotification extends Model
{
    // Properties
    // =========================================================================

    public $id;
    public $creationDate;
    public $type;
    public $deliveryMethod;
    public $body;
    public $message;
    public $subject;
    public $sentOn;
}
