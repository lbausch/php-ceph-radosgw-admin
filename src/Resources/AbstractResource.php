<?php

namespace LBausch\CephRadosgwAdmin\Resources;

use LBausch\CephRadosgwAdmin\ApiRequest;
use LBausch\CephRadosgwAdmin\Client;
use LBausch\CephRadosgwAdmin\Config;
use LBausch\CephRadosgwAdmin\Contracts\ResourceContract;

abstract class AbstractResource implements ResourceContract
{
    /**
     * The endpoint to use.
     */
    protected string $endpoint;

    /**
     * Interact with API.
     */
    protected ApiRequest $api;

    protected Config $config;

    final public function __construct(Client $client)
    {
        $this->api = ApiRequest::make($client->getHttpClient());
        $this->config = $client->getConfig();
    }

    /**
     * Interact with resource using client.
     *
     * static return type is available as of PHP 8
     *
     * @return static
     */
    public static function withClient(Client $client)
    {
        // Use static instead of self, since self is abstract and cannot be instantiated
        return new static($client);
    }
}
