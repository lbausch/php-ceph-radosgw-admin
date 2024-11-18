<?php

namespace LBausch\CephRadosgwAdmin\Middlewares;

use InvalidArgumentException;
use LBausch\CephRadosgwAdmin\Config;
use LBausch\CephRadosgwAdmin\Signature\AbstractSignature;
use Psr\Http\Message\RequestInterface;

class SignatureMiddleware extends AbstractMiddleware
{
    /**
     * Handle signing requests.
     */
    public static function handle(Config $config): callable
    {
        return function (callable $handler) use ($config): callable {
            return function (RequestInterface $request, array $options) use ($handler, $config) {
                // Sign the request
                $request = self::signRequest($request, $options, $config);

                // Remove the signature option
                unset($options[AbstractSignature::SIGNATURE_OPTION]);

                return $handler($request, $options);
            };
        };
    }

    /**
     * Sign request.
     *
     * @param array<mixed, mixed> $options
     */
    public static function signRequest(RequestInterface $request, array $options, Config $config): RequestInterface
    {
        // Check if a specific signature was requested, use v4 by default
        $signature = $options[AbstractSignature::SIGNATURE_OPTION] ?? $config->get('signatureV4');

        if (!class_exists($signature)) {
            throw new InvalidArgumentException('Invalid signature '.$signature);
        }

        /** @var AbstractSignature $signatureService */
        $signatureService = new $signature();

        return $signatureService->signRequest($request, $config);
    }
}
