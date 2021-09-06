<?php

namespace LBausch\CephRadosgwAdmin\Contracts;

use LBausch\CephRadosgwAdmin\Client;

interface ResourceContract
{
    /**
     * Interact with resource using client.
     *
     * @return static
     */
    public static function withClient(Client $client);
}
