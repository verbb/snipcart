<?php
namespace verbb\snipcart\controllers;

use verbb\snipcart\services\Webhooks;
use verbb\snipcart\Snipcart;

use Craft;
use craft\helpers\Json;
use craft\web\Controller;

use Yii;
use ErrorException;
use yii\base\Exception;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use Throwable;

class WebhooksController extends Controller
{
    // Constants
    // =========================================================================

    public const WEBHOOK_ORDER_COMPLETED = 'order.completed';
    public const WEBHOOK_SHIPPINGRATES_FETCH = 'shippingrates.fetch';
    public const WEBHOOK_ORDER_STATUS_CHANGED = 'order.status.changed';
    public const WEBHOOK_ORDER_PAYMENT_STATUS_CHANGED = 'order.paymentStatus.changed';
    public const WEBHOOK_ORDER_TRACKING_NUMBER_CHANGED = 'order.trackingNumber.changed';
    public const WEBHOOK_SUBSCRIPTION_CREATED = 'subscription.created';
    public const WEBHOOK_SUBSCRIPTION_CANCELLED = 'subscription.cancelled';
    public const WEBHOOK_SUBSCRIPTION_PAUSED = 'subscription.paused';
    public const WEBHOOK_SUBSCRIPTION_RESUMED = 'subscription.resumed';
    public const WEBHOOK_SUBSCRIPTION_INVOICE_CREATED = 'subscription.invoice.created';
    public const WEBHOOK_TAXES_CALCULATE = 'taxes.calculate';
    public const WEBHOOK_CUSTOMER_UPDATED = 'customauth:customer_updated';
    public const WEBHOOK_REFUND_CREATED = 'order.refund.created';
    public const WEBHOOK_NOTIFICATION_CREATED = 'order.notification.created';

    public const WEBHOOK_EVENT_MAP = [
        self::WEBHOOK_ORDER_COMPLETED => 'handleOrderCompleted',
        self::WEBHOOK_SHIPPINGRATES_FETCH => 'handleShippingRatesFetch',
        self::WEBHOOK_ORDER_STATUS_CHANGED => 'handleOrderStatusChange',
        self::WEBHOOK_ORDER_PAYMENT_STATUS_CHANGED => 'handleOrderPaymentStatusChange',
        self::WEBHOOK_ORDER_TRACKING_NUMBER_CHANGED => 'handleOrderTrackingNumberChange',
        self::WEBHOOK_SUBSCRIPTION_CREATED => 'handleSubscriptionCreated',
        self::WEBHOOK_SUBSCRIPTION_CANCELLED => 'handleSubscriptionCancelled',
        self::WEBHOOK_SUBSCRIPTION_PAUSED => 'handleSubscriptionPaused',
        self::WEBHOOK_SUBSCRIPTION_RESUMED => 'handleSubscriptionResumed',
        self::WEBHOOK_SUBSCRIPTION_INVOICE_CREATED => 'handleSubscriptionInvoiceCreated',
        self::WEBHOOK_TAXES_CALCULATE => 'handleTaxesCalculate',
        self::WEBHOOK_CUSTOMER_UPDATED => 'handleCustomerUpdated',
        self::WEBHOOK_REFUND_CREATED => 'handleRefundCreated',
        self::WEBHOOK_NOTIFICATION_CREATED => 'handleNotificationCreated',
    ];


    // Properties
    // =========================================================================

    public $enableCsrfValidation = false;

    protected array|int|bool $allowAnonymous = true;

    private static bool $validateWebhook = true;


    // Public Methods
    // =========================================================================

    public function init(): void
    {
        parent::init();

        // Return all controller output as JSON.
        Yii::$app->response->format = Response::FORMAT_JSON;
    }

    public function actionHandle(): Response
    {
        $request = Craft::$app->getRequest();
        $requestBody = $request->getRawBody();

        if ($requestBody === '') {
            $requestBody = file_get_contents('php://input') ?: '';
        }

        Snipcart::info('Incoming webhook request received. method={method} path={pathInfo} bodyLength={bodyLength}', [
            'method' => $request->getMethod(),
            'pathInfo' => $request->getPathInfo(),
            'bodyLength' => strlen($requestBody),
        ]);

        Snipcart::info('Incoming webhook raw body: {rawBody}', [
            'rawBody' => $requestBody,
        ]);

        set_error_handler(static function(int $severity, string $message, string $file, int $line): bool {
            if (!(error_reporting() & $severity)) {
                return false;
            }

            if (in_array($severity, [E_DEPRECATED, E_USER_DEPRECATED], true)) {
                return true;
            }

            throw new ErrorException($message, 0, $severity, $file, $line);
        });

        try {
            $this->requirePostRequest();
            $payload = Json::decode($requestBody, false);

            if ($reason = $this->hasInvalidRequestData($payload)) {
                Snipcart::info('Webhook rejected before processing.', [
                    'reason' => $reason,
                ]);

                return $this->badRequestResponse([
                    'reason' => $reason,
                ]);
            }

            Snipcart::info('Webhook received.', [
                'eventName' => $payload->eventName ?? null,
                'mode' => $payload->mode ?? null,
            ]);

            Snipcart::$plugin->getWebhooks()->setData($payload);

            $response = $this->handleWebhookData($payload->eventName);

            Snipcart::info('Webhook processed successfully: ' . Json::encode([
                'eventName' => $payload->eventName ?? null,
                'statusCode' => $response->statusCode,
                'responseContent' => $response->content,
            ]));

            return $response;
        } catch (Throwable $e) {
            Snipcart::error('Unhandled exception while processing webhook: {message} type={type} file={file} line={line} trace={trace}', [
                'message' => $e->getMessage(),
                'type' => $e::class,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            $response = $this->asJson([
                'success' => false,
                'error' => 'Unhandled webhook exception.',
            ]);
            $response->setStatusCode(500, 'Internal Server Error');

            return $response;
        } finally {
            restore_error_handler();
        }
    }


    // Private Methods
    // =========================================================================

    private function handleWebhookData(string $eventName): Response
    {
        $methodName = self::WEBHOOK_EVENT_MAP[$eventName];

        Snipcart::info('Webhook dispatching to handler.', [
            'eventName' => $eventName,
            'methodName' => $methodName,
        ]);

        if (method_exists(Snipcart::$plugin->getWebhooks(), $methodName)) {
            $responseData = Snipcart::$plugin->getWebhooks()->{$methodName}();

            Snipcart::info('Webhook handler response data prepared: ' . Json::encode([
                'eventName' => $eventName,
                'methodName' => $methodName,
                'responseData' => $responseData,
            ]));

            return $this->asJson($responseData);
        }

        throw new Exception("Invalid Snipcart webhook handler specified for `$eventName`.");
    }

    private function badRequestResponse(array $errors): Response
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

    private function hasInvalidRequestData(mixed $payload): bool|string
    {
        if (self::$validateWebhook && ! $this->requestIsValid()) {
            return 'Could not validate webhook request. Are you Snipcart?';
        }

        if ($payload === null || ! isset($payload->eventName)) {
            return 'NULL request body or missing eventName.';
        }

        if (!isset($payload->content)) {
            return 'Request missing content.';
        }

        if (!isset($payload->mode)) {
            return 'Request missing mode.';
        }

        if (!in_array($payload->mode, [Webhooks::WEBHOOK_MODE_LIVE, Webhooks::WEBHOOK_MODE_TEST], false)) {
            return 'Invalid mode.';
        }

        if (!array_key_exists($payload->eventName, self::WEBHOOK_EVENT_MAP)) {
            return 'Invalid event.';
        }

        return false;
    }

    private function requestIsValid(): bool
    {
        $key = 'x-snipcart-requesttoken';
        $headers = Craft::$app->getRequest()->getHeaders();
        $devMode = Craft::$app->getConfig()->general->devMode;

        if (!$headers->has($key) && $devMode) {
            return true;
        }

        if (!$headers->has($key)) {
            throw new BadRequestHttpException('Invalid request: no request token');
        }

        $token = $headers->get($key);

        if (!is_string($token)) {
            throw new BadRequestHttpException('Invalid request: token can only be a string');
        }

        return Snipcart::$plugin->getApi()->tokenIsValid($token);
    }
}
