<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models\snipcart;

/**
 * https://docs.snipcart.com/v2/api-reference/notifications
 */

class Notification extends \craft\base\Model
{
    const TYPE_COMMENT          = 'Comment';
    const TYPE_INVOICE          = 'Invoice';
    const DELIVERY_METHOD_EMAIL = 'Email';
    const DELIVERY_METHOD_NONE  = 'None';

    /**
     * @var string "0c3ac0bb-a94a-45c5-a4d8-a7934a7f180a"
     */
    public $id;

    /**
     * @var \DateTime "2017-07-09T14:59:57.987Z"
     */
    public $creationDate;

    /**
     * @var string `Comment`, `Invoice`, maybe other possibilities?
     */
    public $type;

    /**
     * @var string `email`
     */
    public $deliveryMethod;

    /**
     * @var string
     */
    public $body;

    /**
     * @var string
     */
    public $message;

    /**
     * @var string
     */
    public $subject;

    /**
     * @var string "2017-06-07T19:09:31.933Z"
     */
    public $sentOn;

    /**
     * @inheritdoc
     */
    public function datetimeAttributes(): array
    {
        return ['creationDate'];
    }


}
