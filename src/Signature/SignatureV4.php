<?php

namespace LBausch\PhpRadosgwAdmin\Signature;

use Aws\Credentials\CredentialsInterface;
use Aws\Signature\SignatureInterface;
use Aws\Signature\SignatureProvider;
use LBausch\PhpRadosgwAdmin\Config;
use Psr\Http\Message\RequestInterface;

class SignatureV4 extends AbstractSignature
{
    /**
     * Sign request.
     */
    public function signRequest(RequestInterface $request, Config $config): RequestInterface
    {
        /** @var CredentialsInterface $credentials */
        $credentials = $config->get('credentials');

        // Setup signature provider to sign requests
        // https://docs.aws.amazon.com/general/latest/gr/sigv4-add-signature-to-request.html
        $signatureProvider = $config->get('signatureProvider', SignatureProvider::defaultProvider());

        // Get a provider for signature version 4
        /** @var SignatureInterface $signature */
        $signature = $signatureProvider('v4', $config->get('service'), $config->get('region'));

        return $signature->signRequest($request, $credentials);
    }
}
