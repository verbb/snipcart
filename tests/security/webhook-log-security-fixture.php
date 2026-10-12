<?php

use craft\config\GeneralConfig;
use craft\log\MessageProcessor;
use craft\web\Request;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonologLogger;
use Monolog\Processor\PsrLogMessageProcessor;
use verbb\base\helpers\Plugin;
use verbb\snipcart\controllers\WebhooksController;
use verbb\snipcart\services\Webhooks;
use verbb\snipcart\Snipcart;
use yii\base\Component;
use yii\i18n\I18N;
use yii\log\Logger;
use yii\web\Response;

// Run with VERBB_SNIPCART_TEST_VENDOR pointing to an installed Craft 5 vendor directory.
$vendorPath = getenv('VERBB_SNIPCART_TEST_VENDOR') ?: dirname(__DIR__, 2) . '/vendor';

if (!is_file($vendorPath . '/autoload.php')) {
    throw new RuntimeException('Set VERBB_SNIPCART_TEST_VENDOR to a Craft 5 vendor directory.');
}

require $vendorPath . '/autoload.php';
require $vendorPath . '/yiisoft/yii2/Yii.php';
require $vendorPath . '/craftcms/cms/src/Craft.php';

spl_autoload_register(static function(string $class): void {
    $prefix = 'verbb\\snipcart\\';

    if (str_starts_with($class, $prefix)) {
        $path = dirname(__DIR__, 2) . '/src/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';

        if (is_file($path)) {
            require $path;
        }
    }
}, true, true);

final class WebhookLogFixtureApp extends Component
{
    public array $loadedModules = [];
    public string $charset = 'UTF-8';
    public string $language = 'en-US';
    public string $sourceLanguage = 'en-US';
    public Request $request;
    public Response $response;
    public I18N $i18n;
    public object $config;

    public function getRequest(): Request { return $this->request; }
    public function getResponse(): Response { return $this->response; }
    public function getI18n(): I18N { return $this->i18n; }
    public function getConfig(): object { return $this->config; }
    public function getTimeZone(): string { return 'UTC'; }
}

final class WebhookLogFixtureService extends Webhooks
{
    public int $calls = 0;
    public mixed $payload = null;

    public function setData($payload): void { $this->payload = $payload; }
    public function handleShippingRatesFetch(): array
    {
        $this->calls++;
        return ['rates' => []];
    }
}

$checks = 0;
$failures = [];
$assert = static function(bool $condition, string $message) use (&$checks, &$failures): void {
    $checks++;

    if (!$condition) {
        $failures[] = $message;
    }
};

$app = new WebhookLogFixtureApp();
Craft::$app = $app;
$app->i18n = new I18N(['translations' => ['*' => ['class' => yii\i18n\PhpMessageSource::class]]]);
$general = (new ReflectionClass(GeneralConfig::class))->newInstanceWithoutConstructor();
$general->usePathInfo = false;
$general->devMode = false;
$app->config = (object)['general' => $general];
$plugin = (new ReflectionClass(Snipcart::class))->newInstanceWithoutConstructor();
$plugin->id = 'snipcart';
$service = new WebhookLogFixtureService();
$plugin->set('webhooks', $service);
$app->loadedModules[Snipcart::class] = $plugin;
Snipcart::$plugin = $plugin;
$controller = (new ReflectionClass(WebhooksController::class))->newInstanceWithoutConstructor();
$logger = new Logger(['flushInterval' => 0]);
Craft::setLogger($logger);
$stream = fopen('php://memory', 'w+');

// Let Base configure the actual target and formatter; only replace its file destination.
$logger->dispatcher = (object)['monologTargetConfig' => [], 'targets' => []];
Plugin::setFileLogging('snipcart', 'verbb\\snipcart\\*', [
    'logContext' => false,
    'logger' => static function($target) use ($stream): MonologLogger {
        $handler = (new StreamHandler($stream))->setFormatter($target->getFormatter());
        return (new MonologLogger('snipcart', [$handler]))
            ->pushProcessor(new PsrLogMessageProcessor())
            ->pushProcessor(new MessageProcessor());
    },
]);
$target = $logger->dispatcher->targets['verbb\\snipcart\\*'];
$logger->dispatcher = null;

$cases = [
    ['normal', 'POST', 'snipcart/webhooks/handle', false, false],
    ['path CRLF', 'POST', "shop\r\n2026-01-01 [ERROR] FORGED", false, false],
    ['form method override', "POST\n2026-01-01 [INFO] FORGED", 'shop', false, false],
    ['header method override', "POST\rFORGED", 'shop', true, false],
    ['literal escapes', 'POST', 'shop\\nFORGED\\r', false, false],
    ['JSON-looking path', 'POST', "{\"path\":\"shop\"}\nFORGED", false, false],
    ['controls and Unicode separators', 'POST', "shop\0\t\x1b\xC2\x85\u{2028}\u{2029}FORGED", false, false],
    ['invalid UTF-8', 'POST', "shop\xFF\nFORGED", false, false],
    ['long path', 'POST', str_repeat('segment/', 1000) . 'tail', false, false],
    ['formatter placeholders', 'POST', '%context%/%extra%/{category}\\nFORGED', false, false],
    ['development webhook', 'POST', 'snipcart/webhooks/handle', false, true],
];

foreach ($cases as [$name, $method, $path, $headerOverride, $devMode]) {
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_POST = $headerOverride ? [] : ['_method' => $method];
    $request = (new ReflectionClass(Request::class))->newInstanceWithoutConstructor();
    $request->generalConfig = $general;
    $request->setQueryParams(['p' => $path, 'action' => 'snipcart/webhooks/handle']);
    // Use Craft's real query-path normalization, without bootstrapping sites or a database.
    (new ReflectionProperty(Request::class, '_path'))->setValue($request, $request->getFullPath());

    if ($headerOverride) {
        $request->getHeaders()->set('X-Http-Method-Override', $method);
    }

    $body = '{"eventName":"shippingrates.fetch","content":{},"mode":"Test"}';
    $request->setRawBody($body);
    $app->request = $request;
    $app->response = new Response();
    $controller->request = $request;
    $controller->response = $app->response;
    $general->devMode = $devMode;
    $logger->messages = [];
    $response = $controller->actionHandle();
    $assert($response->statusCode === ($method !== 'POST' ? 500 : ($devMode ? 200 : 400)), "$name: response behavior changed");
    $assert($service->calls === ($devMode ? 1 : 0), "$name: unexpected webhook dispatch");

    if ($devMode) {
        $assert($response->data === ['rates' => []], "$name: handler response changed");
        $assert($service->payload->eventName === 'shippingrates.fetch', "$name: payload delivery changed");
    } elseif ($method === 'POST') {
        $assert(json_decode($response->content, true)['errors']['reason'] === 'Could not validate webhook request. Are you Snipcart?', "$name: token rejection changed");
    }

    $incoming = array_values(array_filter($logger->messages, static fn($message) => str_starts_with($message[0], 'Incoming webhook request received.')));
    $assert(count($incoming) === 1, "$name: missing incoming-request diagnostic");
    ftruncate($stream, 0);
    rewind($stream);
    $target->messages = $incoming;
    $target->export();
    rewind($stream);
    $output = stream_get_contents($stream);
    $assert(substr_count($output, "\n") === 1 && !str_contains($output, "\r"), "$name: formatter emitted a forged record boundary");
    $assert(!preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]|\xC2\x85|\xE2\x80[\xA8\xA9]/', $output), "$name: raw control or Unicode separator survived");

    // Monolog expands its own placeholders inside messages; test the record boundary only here.
    if ($name === 'formatter placeholders') {
        continue;
    }

    $matched = preg_match('/method=("(?:[^"\\\\]|\\\\.)*") path=("(?:[^"\\\\]|\\\\.)*") bodyLength=(\d+)/', $output, $matches);
    $assert($matched === 1, "$name: missing quoted diagnostics");

    if ($matched) {
        $assert(json_decode($matches[1]) === $request->getMethod(), "$name: method diagnostic changed");
        $expectedPath = json_decode(json_encode($request->getPathInfo(), JSON_INVALID_UTF8_SUBSTITUTE));
        $assert(json_decode($matches[2]) === $expectedPath, "$name: path diagnostic lost information");
        $assert((int)$matches[3] === strlen($body), "$name: body length changed");
    }
}

$_POST = [];

if ($failures) {
    throw new RuntimeException(implode("\n", $failures));
}

echo "Webhook log security fixture passed ($checks checks).\n";
