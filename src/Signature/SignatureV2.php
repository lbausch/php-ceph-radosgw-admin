<?php

namespace LBausch\CephRadosgwAdmin\Signature;

use Aws\Credentials\CredentialsInterface;
use LBausch\CephRadosgwAdmin\Config;
use Psr\Http\Message\RequestInterface;

class SignatureV2 extends AbstractSignature
{
    /**
     * Sign request.
     *
     * @see https://docs.ceph.com/en/latest/radosgw/s3/authentication/
     * @see https://docs.aws.amazon.com/AmazonS3/latest/userguide/RESTAuthentication.html
     */
    public function signRequest(RequestInterface $request, Config $config): RequestInterface
    {
        /** @var CredentialsInterface $credentials */
        $credentials = $config->get('credentials');

        // Get the string to sign
        $stringToSign = $this->stringToSign($request);

        // Calculate signature
        $signature = base64_encode(
            hash_hmac(
                'sha1',
                $stringToSign,
                $credentials->getSecretKey(),
                $binary = true
            )
        );

        // Create request with required headers
        $request = $request->withHeader('Date', gmdate(DATE_RFC2822))
            ->withHeader('Authorization', 'AWS '.$credentials->getAccessKeyId().':'.$signature);

        return $request;
    }

    /**
     * String to sign.
     */
    protected function stringToSign(RequestInterface $request): string
    {
        // StringToSign = HTTP-Verb + "\n" +
        //      Content-MD5 + "\n" +
        //      Content-Type + "\n" +
        //      Date + "\n" +
        //      CanonicalizedAmzHeaders +
        //      CanonicalizedResource;

        return $request->getMethod()."\n"
            .$this->contentMd5($request)."\n"
            .$request->getHeaderLine('Content-Type')."\n"
            .$this->expires($request)."\n"
            .$this->canonicalizedAmzHeaders($request)
            .$this->canonicalizedResource($request);
    }

    /**
     * Get Content-MD5.
     */
    protected function contentMd5(RequestInterface $request): string
    {
        // Respect existing header
        if ($request->hasHeader('Content-MD5')) {
            return $request->getHeaderLine('Content-MD5');
        }

        // Do not calculate MD5 for GET and PUT requests
        if (in_array($request->getMethod(), ['GET', 'PUT'])) {
            return '';
        }

        $body = $request->getBody();

        if (null === $body->getSize() || 0 === $body->getSize()) {
            return '';
        }

        $content = $body->getContents();

        return md5($content);
    }

    /**
     * Get expires.
     */
    public function expires(RequestInterface $request): string
    {
        // Respect existing header
        if ($request->hasHeader('Date')) {
            return $request->getHeaderLine('Date');
        }

        return gmdate(DATE_RFC2822);
    }

    /**
     * Get canonicalized Amz headers.
     *
     * @see https://docs.aws.amazon.com/AmazonS3/latest/userguide/RESTAuthentication.html#RESTAuthenticationConstructingCanonicalizedAmzHeaders
     */
    protected function canonicalizedAmzHeaders(RequestInterface $request): string
    {
        // @TODO

        return '';
    }

    /**
     * Get canonicalized resource.
     *
     * @see https://docs.aws.amazon.com/AmazonS3/latest/userguide/RESTAuthentication.html#ConstructingTheCanonicalizedResourceElement
     */
    protected function canonicalizedResource(RequestInterface $request): string
    {
        // @TODO

        // CanonicalizedResource = [ "/" + Bucket ] +
        // <HTTP-Request-URI, from the protocol name up to the query string> +
        // [ subresource, if present. For example "?acl", "?location", or "?logging"];

        return $request->getUri()->getPath();
    }
}
