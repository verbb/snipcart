<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\controllers;

use workingconcept\snipcart\Snipcart;
use workingconcept\snipcart\WebhookEvent;

use Craft;
use craft\web\Controller;
use craft\elements\Entry;
use craft\mail\Message;
use yii\web\Response;

class ShipStationWebhooksController extends Controller
{
    public $enableCsrfValidation = false; // disable CSRF for this controller

    protected $allowAnonymous = true;
    protected $settings;


    /**
     * Handle the $_POST data that ShipStation sent, which is a raw body of JSON.
     */

    public function actionHandle()
    {
        $this->requirePostRequest();
        $this->settings = Snipcart::$plugin->getSettings();

        $body = json_decode(Craft::$app->request->getRawBody());

        if (is_null($body) or !isset($body->resource_type))
        {
            /*
             * every post should have an eventName property, so we've got empty data or a bad format
             */

            return $this->badResponse([
                'reason' => 'NULL response body or missing resource_type.'
            ]);
        }

        /*
         * respond to different types of Snipcart events—in this case only one
         */

        switch ($body->resource_type)
        {
            case 'ORDER_NOTIFY':
                return $this->processOrderNotifyEvent($body);
                break;
            case 'ITEM_ORDER_NOTIFY':
                return $this->processItemOrderNotifyEvent($body);
                break;
            case 'SHIP_NOTIFY':
                return $this->processShipNotifyEvent($body);
                break;
            case 'ITEM_SHIP_NOTIFY':
                return $this->processShipNotifyEvent($body);
                break;
            default:
                // unsupported event
                return $this->notSupportedResponse();
                break;
        }
    }
    
    private function processOrderNotifyEvent($body)
    {
        return $this->notSupportedResponse();
    }

    private function processItemOrderNotifyEvent($body)
    {
        return $this->notSupportedResponse();
    }

    private function processShipNotifyEvent($body)
    {
        // TODO: notify the customer that their order has shipped + provide tracking number
            // follow $body->resource_url to get more information
        return $this->notSupportedResponse();
    }

    private function processItemShipNotifyEvent($body)
    {
        return $this->notSupportedResponse();
    }


    /**
     * Output a 400 response with an optional JSON error array.
     *
     * @param  array  $errors Array of errors that explain the 400 response
     *
     * @return Response;
     */

    private function badResponse(array $errors)
    {
        $response = Craft::$app->getResponse();

        $response->format  = Response::FORMAT_JSON;
        $response->content = json_encode([
            'success' => false,
            'errors' => $errors
        ]);

        $response->setStatusCode(400, 'Bad Request');

        return $response;
    }


    /**
     * Output a 200 response so Snipcart knows we're okay, but not handling the event.
     *
     * @return void
     */

    private function notSupportedResponse()
    {
        $response = Craft::$app->getResponse();
        $response->setStatusCode(200);

        return $response;
    }

}