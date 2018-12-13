<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\records;

use craft\db\ActiveRecord;

/**
 * Class WebhookLog
 *
 * @package workingconcept\snipcart\records
 *
 * @property int $id
 * @property int $siteId
 * @property string $type
 * @property string $body
 */
class WebhookLog extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%snipcart_webhook_log}}';
    }
}
