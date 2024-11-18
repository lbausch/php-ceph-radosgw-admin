<?php

namespace Tests;

use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use LBausch\CephRadosgwAdmin\Client;
use LBausch\CephRadosgwAdmin\Resources\AbstractResource;
use LBausch\CephRadosgwAdmin\Signature\AbstractSignature;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(\LBausch\CephRadosgwAdmin\ApiRequest::class)]
#[CoversClass(Client::class)]
#[CoversClass(\LBausch\CephRadosgwAdmin\Config::class)]
#[CoversClass(AbstractResource::class)]
#[CoversMethod(\LBausch\CephRadosgwAdmin\Middlewares\SignatureMiddleware::class, 'handle')]
#[CoversMethod(\LBausch\CephRadosgwAdmin\Middlewares\SignatureMiddleware::class, 'signRequest')]
final class SignatureTest extends TestCase
{
    public function testInvalidSignatureClassThrowsException(): void
    {
        $transactions = [];

        $config = $this->getConfigWithMockedHandlers($transactions, [
            new Response(200, [], '{"tenant":"","user_id":"foo","display_name":"baz"}'),
        ]);

        $client = Client::make('http://gateway', 'acesskey', 'secretkey', $config);

        $dummyResource = new class($client) extends AbstractResource {
            public function info(): void
            {
                $this->api->get('foo', [
                    AbstractSignature::SIGNATURE_OPTION => 'foobar',
                ]);
            }
        };

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid signature foobar');

        $dummyResource->info();
    }
}
