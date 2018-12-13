<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\controllers;

use workingconcept\snipcart\Snipcart;

use Craft;
use craft\web\Controller;
use yii\web\Response;

class ShipStationWebhooksController extends Controller
{
    // Properties
    // =========================================================================

    /**
     * @var bool Disable CSRF here since we're expecting and validating outside posts.
     */
    public $enableCsrfValidation = false;

    /**
     * @var bool Allow anonymous, unauthenticated access to our one method.
     */
    protected $allowAnonymous = true;

    /**
     * @var
     */
    protected $settings;


    // Public Methods
    // =========================================================================

    /**
     * Handle the $_POST data that ShipStation sent, which is a raw body of JSON.
     */
    public function actionHandle()
    {
        $this->requirePostRequest();
        $this->settings = Snipcart::$plugin->getSettings();

        $body = json_decode(Craft::$app->request->getRawBody());

        if ($body === null || ! isset($body->resource_type))
        {
            /**
             * Every post should have an eventName property, so we've got empty data or a bad format.
             */

            return $this->badResponse([
                'reason' => 'NULL response body or missing resource_type.'
            ]);
        }

        /**
         * Respond to different types of Snipcart events—in this case only one.
         */
        switch ($body->resource_type)
        {
            case 'ORDER_NOTIFY':
                return $this->handleOrderNotifyEvent($body);
            case 'ITEM_ORDER_NOTIFY':
                return $this->handleItemOrderNotifyEvent($body);
            case 'SHIP_NOTIFY':
                return $this->handleShipNotifyEvent($body);
            case 'ITEM_SHIP_NOTIFY':
                return $this->handleItemShipNotifyEvent($body);
            default:
                return $this->notSupportedResponse();
        }
    }


    // Private Methods
    // =========================================================================

    /**
     * Respond to an order notification. (Currently, we don't.)
     *
     * @param $body Object sent by ShipStation.
     *
     * @return Response
     */
    private function handleOrderNotifyEvent($body): Response
    {
        return $this->notSupportedResponse();
    }

    /**
     * Respond to an *item* order notification. (Currently, we don't.)
     *
     * @param $body Object sent by ShipStation.
     *
     * @return Response
     */
    private function handleItemOrderNotifyEvent($body): Response
    {
        return $this->notSupportedResponse();
    }

    /**
     * Respond to a shipment notification. (Currently, we don't.)
     *
     * @param $body Object sent by ShipStation.
     *
     * @return Response
     */
    private function handleShipNotifyEvent($body): Response
    {
        // TODO: notify customer that the order has shipped + provide tracking number
            // follow $body->resource_url to get more information
        return $this->notSupportedResponse();
    }

    /**
     * Respond to an *item* shipment notification. (Currently, we don't.)
     *
     * @param $body Object sent by ShipStation.
     *
     * @return Response
     */
    private function handleItemShipNotifyEvent($body): Response
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
    private function badResponse(array $errors): Response
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
     * Send back a 200 response so Snipcart knows we're okay but not handling the event.
     *
     * @return Response
     */
    private function notSupportedResponse(): Response
    {
        $response = Craft::$app->getResponse();
        $response->setStatusCode(200);

        return $response;
    }

}