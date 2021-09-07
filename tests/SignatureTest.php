<?php

namespace Tests;

use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use LBausch\CephRadosgwAdmin\Client;
use LBausch\CephRadosgwAdmin\Resources\AbstractResource;
use LBausch\CephRadosgwAdmin\Signature\AbstractSignature;

final class SignatureTest extends TestCase
{
    /**
     * @covers \LBausch\CephRadosgwAdmin\ApiRequest
     * @covers \LBausch\CephRadosgwAdmin\Client
     * @covers \LBausch\CephRadosgwAdmin\Config
     * @covers \LBausch\CephRadosgwAdmin\Resources\AbstractResource
     * @covers \LBausch\CephRadosgwAdmin\Middlewares\SignatureMiddleware::handle
     * @covers \LBausch\CephRadosgwAdmin\Middlewares\SignatureMiddleware::signRequest
     */
    public function testInvalidSignatureClassThrowsException(): void
    {
        $transactions = [];

        $config = $this->getConfigWithMockedHandlers($transactions, [
            new Response(200, [], '{"tenant":"","user_id":"foo","display_name":"baz"}'),
        ]);

        $client = Client::make('http://gateway', 'acesskey', 'secretkey', $config);

        $dummyResource = new class($client) extends AbstractResource {
            public function info()
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
