<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\services;

use workingconcept\snipcart\Snipcart;

use Craft;
use craft\base\Component;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use yii\base\Exception;


/**
 * Class ApiService
 *
 * For interaction directly with the Snipcart API and getting back response data.
 *
 * @package workingconcept\snipcart\services
 */
class ApiService extends Component
{
    // Constants
    // =========================================================================

    const CACHE_KEY_PREFIX = 'snipcart_';

    // Properties
    // =========================================================================

    /**
     * @var string Snipcart's base API URL used for all interactions.
     */
    protected static $apiBaseUrl = 'https://app.snipcart.com/api/';

    /**
     * @var bool Whether or not we have credentials for Snipcart API interaction.
     */
    protected $isLinked;

    /**
     * @var Client
     */
    protected $client;


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->isLinked = isset(Snipcart::$plugin->getSettings()->secretApiKey);
    }

    /**
     * Get an instance of the Guzzle client.
     *
     * @return Client
     * @throws Exception Thrown when we don't have an API key with which to make calls.
     */
    public function getClient(): Client
    {
        if ( ! $this->isLinked)
        {
            throw new Exception('Snipcart plugin not configured.');
        }

        if ($this->client !== null)
        {
            return $this->client;
        }

        return $this->client = new Client([
            'base_uri' => self::$apiBaseUrl,
            'auth' => [
                Snipcart::$plugin->getSettings()->secretApiKey,
                'password'
            ],
            'headers' => [
                'Content-Type' => 'application/json; charset=utf-8',
                'Accept'       => 'application/json, text/javascript, */*; q=0.01',
            ],
            'verify' => false,
            'debug'  => false
        ]);
    }

    /**
     * Perform get with the Snipcart API.
     *
     * @param  string $endpoint    Snipcart API method to be queried
     * @param  array  $parameters  array of parameters to be URL formatted
     * @param  bool   $useCache    whether or not to cache responses
     *
     * @return \stdClass|array     Response as single object or array of objects
     * @throws Exception           Thrown when we don't have an API key with which to make calls.
     */
    public function get(string $endpoint = '', array $parameters = [], bool $useCache = true)
    {
        if ( ! empty($parameters))
        {
            $endpoint .= '?' . http_build_query($parameters);
        }

        $cacheService = Craft::$app->getCache();
        $cacheKey     = self::CACHE_KEY_PREFIX . $endpoint;

        /**
         * make sure our broader settings *and* local preference both allow cache use
         */
        $useCache = $useCache && Snipcart::$plugin->getSettings()->cacheResponses;

        if ($useCache && $cachedResponseData = $cacheService->get($cacheKey))
        {
            return $cachedResponseData;
        }

        try
        {
            $response     = $this->getClient()->get($endpoint);
            $responseData = $this->prepResponseData($response->getBody());
        }
        catch(RequestException $exception)
        {
            $this->handleRequestException($exception, $endpoint);
            return [];
        }

        if ($useCache)
        {
            $cacheService->set($cacheKey, $responseData, Snipcart::$plugin->getSettings()->cacheDurationLimit);
        }

        return $responseData;
    }

    /**
     * Perform post request to the Snipcart API.
     *
     * @param  string $endpoint    Snipcart API method to receive post
     * @param  array  $data        array of post values to be formatted and sent
     *
     * @return \stdClass|array     Response object or array of objects
     * @throws Exception           Thrown when we don't have an API key with which to make calls.
     */
    public function post(string $endpoint = '', array $data = [])
    {
        try
        {
            $response = $this->getClient()->post($endpoint, [
                \GuzzleHttp\RequestOptions::JSON => $data
            ]);

            $responseData = $this->prepResponseData($response->getBody());
        }
        catch (RequestException $exception)
        {
            $this->handleRequestException($exception, $endpoint);
            return [];
        }

        return $responseData;
    }

    /**
     * Ask Snipcart whether its provided token is genuine
     * (We use this for webhook posts to be sure they came from Snipcart)
     *
     * Tokens are deleted after this call, so it can only be used once to verify,
     * and tokens also expire in one hour. Expect a 404 if the token is deleted
     * or if it expires.
     *
     * @param string  $token  token to be validated, probably from $_POST['HTTP_X_SNIPCART_REQUESTTOKEN']
     *
     * @return bool
     * @throws Exception      Thrown when we don't have an API key with which to make calls.
     */
    public function tokenIsValid($token): bool
    {
        $response = $this->get("requestvalidation/{$token}", null, false);

        return isset($response->token) && $response->token === $token;
    }


    // Private Methods
    // =========================================================================

    /**
     * Take the raw response body and give it back as data that's ready to use.
     *
     * @param $body
     *
     * @return \stdClass|array
     */
    private function prepResponseData($body)
    {
        /**
         * get response data as object, not an associative array
         */
        return json_decode($body, false);
    }

    /**
     * Handle a failed request.
     *
     * @param RequestException $exception  the exception that was thrown
     * @param string           $endpoint   the endpoint that was queried
     */
    private function handleRequestException(RequestException $exception, string $endpoint)
    {
        // get the status code, which should have been 200 or 201 if all went well
        $statusCode = $exception->getResponse()->getStatusCode() ?? null;

        // if there's a response we'll use its body, otherwise default to the request URI
        $reason = $exception->getResponse()->getBody() ?? null;

        if ($statusCode !== null && $reason !== null)
        {
            Craft::warning(sprintf('Snipcart API responded with %d: %s', $statusCode, $reason));
        }
        else
        {
            Craft::warning(sprintf('Snipcart API request to %s failed.', $endpoint));
        }
    }
}