<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\controllers;

use craft\web\Controller;
use yii\web\Response;

class TestController extends Controller
{
    public $enableCsrfValidation = false; // disable CSRF for this controller

    protected array|int|bool $allowAnonymous = true;

    /**
     * Provides a simple webhook that makes sure the plugin is alive
     * and taking requests. Useful for keyword-based uptime check services.
     *
     * /actions/snipcart/test/check-health
     */
    public function actionCheckHealth(): Response
    {
        $this->requirePostRequest();

        return $this->asJson([
            'status' => 'healthy',
        ]);
    }
}
