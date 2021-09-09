<?php

namespace LBausch\CephRadosgwAdmin\Contracts;

use LBausch\CephRadosgwAdmin\Config;

interface MiddlewareContract
{
    /**
     * Handle request.
     */
    public static function handle(Config $config): callable;
}
