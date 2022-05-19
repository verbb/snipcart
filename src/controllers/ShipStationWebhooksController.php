<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\controllers;

use Craft;

use craft\helpers\Json;
use craft\web\Controller;
use yii\web\Response;

class ShipStationWebhooksController extends Controller
{
    /**
     * @var bool Disable CSRF here since we're expecting and validating outside posts.
     */
    public $enableCsrfValidation = false;

    /**
     * @var bool Allow anonymous, unauthenticated access to our one method.
     */
    protected array|bool|int $allowAnonymous = true;

    /**
     * Handles the $_POST data that ShipStation sent, which is a raw body of JSON.
     */
    public function actionHandle()
    {
        $this->requirePostRequest();
        $body = Json::decode(Craft::$app->request->getRawBody(), false);

        if ($body === null || ! isset($body->resource_type)) {
            /**
             * Every post should have an eventName property, so we've got
             * empty data or a bad format.
             */
            return $this->badResponse([
                'reason' => 'NULL response body or missing resource_type.',
            ]);
        }

        return match ($body->resource_type) {
            'ORDER_NOTIFY' => $this->handleOrderNotifyEvent($body),
            'ITEM_ORDER_NOTIFY' => $this->handleItemOrderNotifyEvent($body),
            'SHIP_NOTIFY' => $this->handleShipNotifyEvent($body),
            'ITEM_SHIP_NOTIFY' => $this->handleItemShipNotifyEvent($body),
            default => $this->notSupportedResponse(),
        };
    }

    /**
     * Responds to an order notification. (Currently, we don’t.)
     *
     * @param object $body ShipStation webhook payload
     */
    private function handleOrderNotifyEvent($body): Response
    {
        return $this->notSupportedResponse();
    }

    /**
     * Responds to an *item* order notification. (Currently, we don’t.)
     *
     * @param object $body ShipStation webhook payload
     */
    private function handleItemOrderNotifyEvent($body): Response
    {
        return $this->notSupportedResponse();
    }

    /**
     * Responds to a shipment notification. (Currently, we don’t.)
     *
     * @param object $body ShipStation webhook payload
     */
    private function handleShipNotifyEvent($body): Response
    {
        // TODO: notify customer that the order has shipped + provide tracking number
        // follow $body->resource_url to get more information
        return $this->notSupportedResponse();
    }

    /**
     * Responds to an *item* shipment notification. (Currently, we don’t.)
     *
     * @param object $body ShipStation webhook payload
     */
    private function handleItemShipNotifyEvent($body): Response
    {
        return $this->notSupportedResponse();
    }

    /**
     * Outputs a 400 response with an optional JSON error array.
     *
     * @param  array  $errors Array of errors that explain the 400 response
     * @return Response;
     */
    private function badResponse(array $errors): Response
    {
        $response = Craft::$app->getResponse();

        $response->format = Response::FORMAT_JSON;
        $response->content = json_encode([
            'success' => false,
            'errors' => $errors,
        ], JSON_THROW_ON_ERROR);

        $response->setStatusCode(400, 'Bad Request');

        return $response;
    }

    /**
     * Sends back a 200 response so ShipStation knows we’re okay
     * but not handling the event.
     */
    private function notSupportedResponse(): Response
    {
        $response = Craft::$app->getResponse();
        $response->setStatusCode(200);

        return $response;
    }
}
