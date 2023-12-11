<?php
namespace verbb\snipcart\models;

use craft\base\Model;

class WebhookLog extends Model
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $siteId;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $body;

    /**
     * @var
     */
    public $dateCreated;

    /**
     * @var
     */
    public $dateUpdated;

    /**
     * @var string
     */
    public $uid;
}
