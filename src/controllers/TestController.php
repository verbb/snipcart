<?php
namespace verbb\snipcart\controllers;

use craft\web\Controller;

use yii\web\Response;

class TestController extends Controller
{
    // Properties
    // =========================================================================

    public $enableCsrfValidation = false; // disable CSRF for this controller

    protected array|int|bool $allowAnonymous = true;


    // Public Methods
    // =========================================================================

    public function actionCheckHealth(): Response
    {
        $this->requirePostRequest();

        return $this->asJson([
            'status' => 'healthy',
        ]);
    }
}
