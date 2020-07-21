<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\controllers;

use craft\web\Controller;

class TestController extends Controller
{
    public $enableCsrfValidation = false; // disable CSRF for this controller

    protected $allowAnonymous = true;
    
    /**
     * Provides a simple webhook that makes sure the plugin is alive
     * and taking requests. Useful for keyword-based uptime check services.
     *
     * /actions/snipcart/test/check-health
     */
    public function actionCheckHealth(): \yii\web\Response
    {
        $this->requirePostRequest();
        
        return $this->asJson([
            'status' => 'healthy'
        ]);
    }

}
