<?php
namespace verbb\snipcart\controllers;

use Craft;
use craft\helpers\Json;
use craft\web\Controller;

use yii\web\Response;

class ShipStationWebhooksController extends Controller
{
    // Properties
    // =========================================================================

    public $enableCsrfValidation = false;

    protected array|bool|int $allowAnonymous = true;


    // Public Methods
    // =========================================================================

    public function actionHandle(): Response
    {
        $this->requirePostRequest();

        $body = Json::decode(Craft::$app->request->getRawBody(), false);

        if ($body === null || ! isset($body->resource_type)) {
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


    // Private Methods
    // =========================================================================

    private function handleOrderNotifyEvent(object $body): Response
    {
        return $this->notSupportedResponse();
    }

    private function handleItemOrderNotifyEvent(object $body): Response
    {
        return $this->notSupportedResponse();
    }

    private function handleShipNotifyEvent(object $body): Response
    {
        // TODO: notify customer that the order has shipped + provide tracking number
        // follow $body->resource_url to get more information
        return $this->notSupportedResponse();
    }

    private function handleItemShipNotifyEvent(object $body): Response
    {
        return $this->notSupportedResponse();
    }

    private function badResponse(array $errors): Response
    {
        $response = Craft::$app->getResponse();

        $response->format = Response::FORMAT_JSON;
        $response->content = Json::encode([
            'success' => false,
            'errors' => $errors,
        ]);

        $response->setStatusCode(400, 'Bad Request');

        return $response;
    }

    private function notSupportedResponse(): Response
    {
        $response = Craft::$app->getResponse();
        $response->setStatusCode(200);

        return $response;
    }
}
