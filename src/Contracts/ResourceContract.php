<?php

namespace LBausch\PhpRadosgwAdmin\Contracts;

use LBausch\PhpRadosgwAdmin\Client;

interface ResourceContract
{
    /**
     * Interact with resource using client.
     *
     * @return static
     */
    public static function withClient(Client $client);
}
