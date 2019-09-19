<?php

namespace SpringExpress\Http;

use Closure;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Http client service
 * @author abel
 *
 */
class HttpClient
{
    /**
     * $baseUrl
     * @var string
     */
    protected $baseUrl;

    /**
     * $headers
     * @var array
     */
    protected $headers;

    /**
     * $config
     * @var array
     */
    protected $config;

    /**
     * $beforeRequest
     * @var Closure
     */
    protected $beforeRequest;

    /**
     * $http client
     * @var Client
     */
    protected $httpClient;

    /**
     * Construct
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
        $baseUrl = $config['base_url'] ?? '';
        $this->setBaseUrl($baseUrl);
    }

    /**
     * Set base url
     * @param string $baseUrl
     * @return HttpClient
     */
    public function setBaseUrl(string $baseUrl)
    {
        if (!empty($baseUrl)) {
            $this->baseUrl = rtrim($baseUrl, '/') . '/';
        }
        return $this;
    }

    /**
     * Set headers
     * @param array $headers
     * @return HttpClient
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * Set before request
     * @param Closure $handler
     * @return HttpClient
     */
    public function setBeforeRequest(Closure $handler)
    {
        $this->beforeRequest = $handler;
        return $this;
    }

    /**
     * Http get request
     * @param string $action
     * @param array $querys
     * @param bool $json
     * @return array|mixed|string
     * @throws GuzzleException
     */
    public function get(string $action, array $querys = [], $json = true)
    {
        return $this->request($action, 'GET', ['query' => $querys], $json);
    }

    /**
     * Http post request
     * @param string $action
     * @param array $data
     * @param array $querys
     * @param bool $json
     * @return array|mixed|string
     * @throws GuzzleException
     */
    public function post(string $action, array $data = [], $querys = [], $json = true)
    {
        return $this->request($action, 'POST', ['form_params' => $data, 'query' => $querys], $json);
    }

    /**
     * Http post json request
     * @param string $action
     * @param array $data
     * @param array $querys
     * @return array|mixed|string
     * @throws GuzzleException
     */
    public function postJson(string $action, array $data = [], array $querys = [])
    {
        return $this->request($action, 'POST', ['json' => $data, 'query' => $querys]);
    }

    /**
     * Http down load
     * @param string $action
     * @param array $querys
     * @return void
     */
    public function download(string $action, array $querys = [])
    {
        //todo
    }

    /**
     * Http request
     * @param string $action
     * @param string $method
     * @param array $options
     * @param bool $json
     * @return array|mixed|string
     * @throws GuzzleException
     */
    public function request(string $action, string $method = 'GET', $options = [], $json = true)
    {
        //pre handle
        /* if (is_callable($this->beforeRequest)) {
            call_user_func_array($this->beforeRequest, [$this, $action, $method, $options]);
        } */
        
        $method = empty($method) ? 'GET' : strtoupper($method);
        try {
            $response = $this->getHttpClient()->request($method, $this->getRequestUrl($action), $options);
            $contents = $response->getBody()->getContents();
            
            if ($json) {
                return !empty($contents) ? json_decode($contents, true) : [];
            }
            
            return $contents;
        } catch (Exception $e) {
            return ['status' => 0, 'message' => $e->getCode() . ' ' . $e->getMessage()];
        }
    }

    /**
     * Get http client
     * @return Client
     */
    public function getHttpClient()
    {
        return $this->httpClient ?: $this->httpClient = new Client();
    }

    /**
     * Get Request url
     * @param string $action
     * @return string
     */
    public function getRequestUrl(string $action)
    {
        if (empty($action)) {
            return $this->baseUrl;
        }
        return $this->baseUrl . ltrim($action, '/');
    }
}
