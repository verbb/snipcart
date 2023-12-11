<?php
namespace verbb\snipcart\migrations;

use verbb\snipcart\controllers\WebhooksController;
use verbb\snipcart\db\Table;

use craft\db\Migration;

class m200921_165936_webhook_types extends Migration
{
    // Public Methods
    // =========================================================================

    public function safeUp(): void
    {
        if ($this->getDb()->tableExists(Table::WEBHOOK_LOG)) {
            $typeValues = array_keys(WebhooksController::WEBHOOK_EVENT_MAP);
            
            $this->alterColumn(Table::WEBHOOK_LOG, 'type', $this->enum('type', $typeValues));
        }
    }

    public function safeDown(): bool
    {
        echo "m200921_165936_webhook_types cannot be reverted.\n";
        return false;
    }
}
