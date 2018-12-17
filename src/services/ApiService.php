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
 * @property bool $isLinked
 */
class ApiService extends Component
{
    // Constants
    // =========================================================================

    /**
     * @var string Characters to prepend to any cache keys that are used.
     */
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
     * @throws \Exception  Thrown when there isn't an API key to authenticate requests.
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
     * @return \stdClass|array|null  Response as single object or array of objects, or null for an invalid response.
     * @throws \Exception            Thrown when there isn't an API key to authenticate requests.
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

        $responseData = $this->_getRequest($endpoint);

        if ($responseData && $useCache)
        {
            $cacheService->set(
                $cacheKey,
                $responseData,
                Snipcart::$plugin->getSettings()->cacheDurationLimit
            );
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
     * @throws \Exception           Thrown when we don't have an API key with which to make calls.
     */
    public function post(string $endpoint = '', array $data = [])
    {
        return $this->_postRequest($endpoint, $data);
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
     * @throws \Exception Thrown when there isn't an API key to authenticate requests.
     */
    public function tokenIsValid($token): bool
    {
        $response = $this->get("requestvalidation/{$token}", null, false);

        return isset($response->token) && $response->token === $token;
    }


    // Private Methods
    // =========================================================================

    /**
     * Send a get request to the Snipcart API.
     * 
     * @param string $endpoint
     *
     * @return mixed
     * @throws \Exception Thrown if we don't have an API key to make an authenticated request.
     */
    private function _getRequest(string $endpoint)
    {
        try
        {
            $response     = $this->getClient()->get($endpoint);
            $responseData = $this->_prepResponseData($response->getBody());

            return $responseData;
        }
        catch(RequestException $exception)
        {
            return $this->_handleRequestException($exception, $endpoint);
        }
    }

    /**
     * Send a post request to the Snipcart API.
     * 
     * @param string $endpoint
     * @param array  $data
     *
     * @return mixed
     * @throws \Exception Thrown if we don't have an API key to make an authenticated request.
     */
    private function _postRequest(string $endpoint, array $data = [])
    {
        try
        {
            $response = $this->getClient()->post($endpoint, [
                \GuzzleHttp\RequestOptions::JSON => $data
            ]);

            return $this->_prepResponseData($response->getBody());
        }
        catch (RequestException $exception)
        {
            return $this->_handleRequestException($exception, $endpoint);
        }
    }

    /**
     * Take the raw response body and give it back as data that's ready to use.
     *
     * @param $body
     *
     * @return mixed Appropriate PHP type, or null if json cannot be decoded or encoded data is deeper than the recursion limit.
     */
    private function _prepResponseData($body)
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
     *
     * @return null
     */
    private function _handleRequestException(RequestException $exception, string $endpoint)
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

        return null;
    }
}