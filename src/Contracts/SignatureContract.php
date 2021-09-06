<?php

namespace LBausch\CephRadosgwAdmin\Contracts;

use LBausch\CephRadosgwAdmin\Config;
use Psr\Http\Message\RequestInterface;

interface SignatureContract
{
    /**
     * Sign a request.
     */
    public function signRequest(RequestInterface $request, Config $config): RequestInterface;
}
