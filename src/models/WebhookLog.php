<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models;

use craft\base\Model;

class WebhookLog extends Model
{
    /**
     * @var
     */
    public $id;

    /**
     * @var
     */
    public $siteId;

    /**
     * @var
     */
    public $type;

    /**
     * @var
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
     * @var
     */
    public $uid;

    public function init()
    {
        parent::init();
    }

    /**
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();

        return $rules;
    }
}
