<?php

namespace LBausch\PhpRadosgwAdmin\Resources;

use LBausch\PhpRadosgwAdmin\ApiRequest;
use LBausch\PhpRadosgwAdmin\Client;
use LBausch\PhpRadosgwAdmin\Contracts\ResourceContract;

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

    final public function __construct(Client $client)
    {
        $this->api = ApiRequest::make($client->getHttpClient());
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
