<?php
namespace verbb\snipcart\console\controllers;

use verbb\snipcart\db\Table;

use Craft;

use yii\console\Controller;
use yii\console\ExitCode;

class CleanupController extends Controller
{
    // Public Methods
    // =========================================================================

    public function actionIndex(): bool
    {
        $tables = [
            Table::PRODUCT_DETAILS,
        ];

        foreach ($tables as $table) {
            echo "Dropping $table...";
            Craft::$app->getDb()->createCommand()->dropTable($table)->execute();
            echo "dropped\r\n";
        }

        return ExitCode::OK;
    }
}
