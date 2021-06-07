<?php

namespace LBausch\PhpRadosgwAdmin\Contracts;

use LBausch\PhpRadosgwAdmin\Config;

interface MiddlewareContract
{
    /**
     * Handle request.
     */
    public static function handle(Config $config): callable;
}
