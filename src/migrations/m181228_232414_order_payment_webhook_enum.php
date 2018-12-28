<?php

namespace workingconcept\snipcart\migrations;

use workingconcept\snipcart\controllers\WebhooksController;
use craft\db\Migration;

/**
 * m181228_232414_order_payment_webhook_enum migration.
 */
class m181228_232414_order_payment_webhook_enum extends Migration
{
    public $tableName = '{{%snipcart_webhook_log}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $typeValues = array_keys(WebhooksController::WEBHOOK_EVENT_MAP);

        // adds set that now includes `order.paymentStatus.changed`
        $this->alterColumn(
            $this->tableName,
            'type',
            $this->enum('type', $typeValues)
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m181228_232414_order_payment_webhook_enum cannot be reverted.\n";
        return false;
    }
}
