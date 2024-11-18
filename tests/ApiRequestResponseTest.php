<?php

namespace Tests;

use GuzzleHttp\Psr7\Response;
use LBausch\CephRadosgwAdmin\ApiException;
use LBausch\CephRadosgwAdmin\Client;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversMethod(\LBausch\CephRadosgwAdmin\ApiRequest::class, '__construct')]
#[CoversMethod(\LBausch\CephRadosgwAdmin\ApiRequest::class, 'delete')]
#[CoversMethod(\LBausch\CephRadosgwAdmin\ApiRequest::class, 'get')]
#[CoversMethod(\LBausch\CephRadosgwAdmin\ApiRequest::class, 'make')]
#[CoversMethod(\LBausch\CephRadosgwAdmin\ApiRequest::class, 'request')]
#[CoversMethod(\LBausch\CephRadosgwAdmin\ApiResponse::class, '__construct')]
#[CoversMethod(\LBausch\CephRadosgwAdmin\ApiResponse::class, 'failed')]
#[CoversMethod(\LBausch\CephRadosgwAdmin\ApiResponse::class, 'fromResponse')]
#[CoversMethod(\LBausch\CephRadosgwAdmin\ApiResponse::class, 'get')]
#[CoversMethod(\LBausch\CephRadosgwAdmin\ApiResponse::class, 'getResponse')]
#[CoversMethod(\LBausch\CephRadosgwAdmin\ApiResponse::class, 'has')]
#[CoversMethod(\LBausch\CephRadosgwAdmin\ApiResponse::class, 'shouldThrowException')]
#[CoversMethod(\LBausch\CephRadosgwAdmin\ApiResponse::class, 'succeeded')]
#[CoversClass(Client::class)]
#[CoversClass(\LBausch\CephRadosgwAdmin\Config::class)]
#[CoversClass(\LBausch\CephRadosgwAdmin\Middlewares\SignatureMiddleware::class)]
#[CoversClass(\LBausch\CephRadosgwAdmin\Resources\AbstractResource::class)]
#[CoversClass(\LBausch\CephRadosgwAdmin\Resources\User::class)]
#[CoversClass(\LBausch\CephRadosgwAdmin\Signature\SignatureV4::class)]
final class ApiRequestResponseTest extends TestCase
{
    public function testRequestAndResponse(): void
    {
        $transactions = [];

        $config = $this->getConfigWithMockedHandlers($transactions, [
            new Response(200, [], '{"foo":"bar"}'),
        ]);

        $client = Client::make('http://gateway:8000', 'accesskey', 'secretkey', $config);

        $response = $client->user()->list();

        $this->assertInstanceOf(Response::class, $response->getResponse());
        $this->assertTrue($response->has('foo'));
        $this->assertFalse($response->failed());

        $this->assertSame(['foo' => 'bar'], $response->get());
        $this->assertSame('bar', $response->get('foo'));
        $this->assertSame('baz', $response->get('foobar', 'baz'));
    }

    public function testExceptionIsThrown(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('NoSuchUser');

        $transactions = [];

        $config = $this->getConfigWithMockedHandlers($transactions, [
            new Response(404, [], '{"Code":"NoSuchUser","RequestId":"foo","HostId":"bar"}'),
        ]);
        $config->set('httpClientConfig', ['http_errors' => false]);

        $client = Client::make('http://gateway:8000', 'accesskey', 'secretkey', $config);

        // Trigger the exception
        $client->user()->remove('foobar');
    }
}
