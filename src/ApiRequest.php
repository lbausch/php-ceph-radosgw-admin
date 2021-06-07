<?php

namespace LBausch\PhpRadosgwAdmin;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;

class ApiRequest
{
    /**
     * HTTP client.
     */
    protected ClientInterface $httpClient;

    protected function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Factory method.
     */
    public static function make(ClientInterface $httpClient): self
    {
        return new self($httpClient);
    }

    /**
     * Perform request, wrapper around Guzzle.
     */
    public function request(string $method, string $uri, array $options = []): ResponseInterface
    {
        return $this->httpClient->request($method, $uri, $options);
    }

    /**
     * Perform GET request.
     */
    public function get(string $uri, array $options = []): ApiResponse
    {
        $response = $this->request('GET', $uri, $options);

        return ApiResponse::fromResponse($response);
    }

    /**
     * Perform PUT request.
     */
    public function put(string $uri, array $options = []): ApiResponse
    {
        $response = $this->request('PUT', $uri, $options);

        return ApiResponse::fromResponse($response);
    }

    /**
     * Perform POST request.
     */
    public function post(string $uri, array $options = []): ApiResponse
    {
        $response = $this->request('POST', $uri, $options);

        return ApiResponse::fromResponse($response);
    }

    /**
     * Perform DELETE request.
     */
    public function delete(string $uri, array $options = []): ApiResponse
    {
        $response = $this->request('DELETE', $uri, $options);

        return ApiResponse::fromResponse($response);
    }
}
