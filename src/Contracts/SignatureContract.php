<?php

namespace LBausch\PhpRadosgwAdmin\Contracts;

use LBausch\PhpRadosgwAdmin\Config;
use Psr\Http\Message\RequestInterface;

interface SignatureContract
{
    /**
     * Sign a request.
     */
    public function signRequest(RequestInterface $request, Config $config): RequestInterface;
}
