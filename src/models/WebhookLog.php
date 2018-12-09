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

class WebhookLog extends Model
{
    public $id;
    public $siteId;
    public $type;
    public $body;
    public $dateCreated;
    public $dateUpdated;
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
