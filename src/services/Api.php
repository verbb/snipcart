<?php
namespace verbb\snipcart\services;

use verbb\snipcart\Snipcart;

use Craft;
use craft\base\Component;
use craft\helpers\App;
use craft\helpers\Json;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

use yii\base\Exception;
use yii\caching\TagDependency;

use stdClass;

class Api extends Component
{
    // Static Methods
    // =========================================================================

    public static function invalidateCache(): void
    {
        TagDependency::invalidate(Craft::$app->getCache(), self::CACHE_TAG);

        Snipcart::log('API caches cleared.');
    }


    // Constants
    // =========================================================================

    public const CACHE_TAG = 'snipcart-api-cache';
    public const CACHE_KEY_PREFIX = 'snipcart_';


    // Properties
    // =========================================================================

    protected static string $apiBaseUrl = 'https://app.snipcart.com/api/';

    protected bool $isLinked = false;
    protected ?Client $client = null;


    // Public Methods
    // =========================================================================

    public function init(): void
    {
        parent::init();

        $this->isLinked = (bool)Snipcart::$plugin->getSettings()->getSecretKey();
    }

    public function getClient(): Client
    {
        if (!$this->isLinked) {
            throw new Exception('Snipcart plugin not configured.');
        }

        if ($this->client instanceof Client) {
            return $this->client;
        }

        $secretKey = Snipcart::$plugin->getSettings()->getSecretKey();

        $clientConfig = [
            'base_uri' => self::$apiBaseUrl,
            'auth' => [$secretKey, 'password'],
            'headers' => [
                'Content-Type' => 'application/json; charset=utf-8',
                'Accept' => 'application/json',
            ],
            'verify' => false,
            'debug' => false,
        ];

        return $this->client = Craft::createGuzzleClient($clientConfig);
    }

    public function get(string $endpoint, array $parameters = [], bool $useCache = true): array|stdClass|null
    {
        if ($parameters !== []) {
            $endpoint .= '?' . http_build_query($parameters);
        }

        $cacheService = Craft::$app->getCache();
        $cacheKey = self::CACHE_KEY_PREFIX . $endpoint;

        $useCache = $useCache && Snipcart::$plugin->getSettings()->cacheResponses;

        if ($useCache && $cachedResponseData = $cacheService->get($cacheKey)) {
            return $cachedResponseData;
        }

        $responseData = $this->getRequest($endpoint);

        if ($responseData && $useCache) {
            $cacheService->set($cacheKey, $responseData, Snipcart::$plugin->getSettings()->cacheDurationLimit, new TagDependency([
                'tags' => [self::CACHE_TAG],
            ]));
        }

        return $responseData;
    }

    public function post(string $endpoint, array $data = []): array|stdClass|null
    {
        return $this->postRequest($endpoint, $data);
    }

    public function put(string $endpoint, array $data = []): array|stdClass|null
    {
        return $this->putRequest($endpoint, $data);
    }

    public function delete(string $endpoint, array $data = []): array|stdClass|null
    {
        return $this->deleteRequest($endpoint, $data);
    }

    public function tokenIsValid(string $token): bool
    {
        $response = $this->getRequest("requestvalidation/$token");

        return isset($response->token) && $response->token === $token;
    }


    // Private Methods
    // =========================================================================

    private function getRequest(string $endpoint): mixed
    {
        try {
            $response = $this->getClient()->get($endpoint);
            
            return $this->prepResponseData($response->getBody());
        } catch (RequestException $requestException) {
            return $this->handleRequestException($requestException, $endpoint);
        }
    }

    private function postRequest(string $endpoint, array $data = []): mixed
    {
        try {
            $response = $this->getClient()->post($endpoint, [
                RequestOptions::JSON => $data,
            ]);

            return $this->prepResponseData($response->getBody());
        } catch (RequestException $requestException) {
            return $this->handleRequestException($requestException, $endpoint);
        }
    }

    private function putRequest(string $endpoint, array $data = []): mixed
    {
        try {
            $response = $this->getClient()->put($endpoint, [
                RequestOptions::JSON => $data,
            ]);

            return $this->prepResponseData($response->getBody());
        } catch (RequestException $requestException) {
            return $this->handleRequestException($requestException, $endpoint);
        }
    }

    private function deleteRequest(string $endpoint, array $data = []): mixed
    {
        try {
            $response = $this->getClient()->delete($endpoint, [
                RequestOptions::JSON => $data,
            ]);

            return $this->prepResponseData($response->getBody());
        } catch (RequestException $requestException) {
            return $this->handleRequestException($requestException, $endpoint);
        }
    }

    private function prepResponseData(StreamInterface $stream): mixed
    {
        return Json::decode($stream, false);
    }

    private function handleRequestException(RequestException $requestException, string $endpoint): void
    {
        $statusCode = null;
        $reason = null;

        if (($response = $requestException->getResponse()) instanceof ResponseInterface) {
            $statusCode = $response->getStatusCode();
            $reason = $response->getBody();
        }

        if ($statusCode !== null && $reason instanceof StreamInterface) {
            Snipcart::error('Snipcart API responded with {code}: {reason}', [
                'code' => $statusCode,
                'reason' => $reason,
            ]);

            if ($statusCode === 401) {
                throw new Exception('Unauthorized; make sure Snipcart API credentials are valid.');
            }

            if ($statusCode === 500) {
                throw new Exception('Snipcart API responded with 500 error.');
            }
        } else {
            Snipcart::error('Snipcart API request to {endpoint} failed.', [
                'endpoint' => $endpoint,
            ]);
        }
    }
}
