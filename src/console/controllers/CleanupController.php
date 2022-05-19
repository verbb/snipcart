<?php

namespace fostercommerce\snipcart\console\controllers;

use Craft;
use fostercommerce\snipcart\db\Table;
use yii\console\Controller;

use yii\console\ExitCode;

/**
 * CleanupController
 * Run the console command craft snipcart/cleanup
 * To remove the legacy product_details table
 */
class CleanupController extends Controller
{
    public function actionIndex(): bool
    {
        $tables = [
            Table::PRODUCT_DETAILS,
        ];

        foreach ($tables as $table) {
            echo sprintf('Dropping %s...', $table);
            Craft::$app->getDb()->createCommand()->dropTable($table)->execute();
            echo "dropped\r\n";
        }

        return ExitCode::OK;
    }
}
