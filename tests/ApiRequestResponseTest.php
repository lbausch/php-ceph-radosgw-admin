<?php

namespace Tests;

use GuzzleHttp\Psr7\Response;
use LBausch\CephRadosgwAdmin\ApiException;
use LBausch\CephRadosgwAdmin\Client;

final class RequestResponseTest extends TestCase
{
    /**
     * @covers \LBausch\CephRadosgwAdmin\ApiRequest::__construct
     * @covers \LBausch\CephRadosgwAdmin\ApiRequest::get
     * @covers \LBausch\CephRadosgwAdmin\ApiRequest::make
     * @covers \LBausch\CephRadosgwAdmin\ApiRequest::request
     * @covers \LBausch\CephRadosgwAdmin\ApiResponse::__construct
     * @covers \LBausch\CephRadosgwAdmin\ApiResponse::failed
     * @covers \LBausch\CephRadosgwAdmin\ApiResponse::fromResponse
     * @covers \LBausch\CephRadosgwAdmin\ApiResponse::get
     * @covers \LBausch\CephRadosgwAdmin\ApiResponse::getResponse
     * @covers \LBausch\CephRadosgwAdmin\ApiResponse::has
     * @covers \LBausch\CephRadosgwAdmin\ApiResponse::shouldThrowException
     * @covers \LBausch\CephRadosgwAdmin\ApiResponse::succeeded
     * @covers \LBausch\CephRadosgwAdmin\Client
     * @covers \LBausch\CephRadosgwAdmin\Config
     * @covers \LBausch\CephRadosgwAdmin\Middlewares\SignatureMiddleware
     * @covers \LBausch\CephRadosgwAdmin\Resources\AbstractResource
     * @covers \LBausch\CephRadosgwAdmin\Resources\User
     * @covers \LBausch\CephRadosgwAdmin\Signature\SignatureV4
     */
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

        $this->assertEquals(['foo' => 'bar'], $response->get());
        $this->assertEquals('bar', $response->get('foo'));
        $this->assertEquals('baz', $response->get('foobar', 'baz'));
    }

    /**
     * @covers \LBausch\CephRadosgwAdmin\ApiRequest::__construct
     * @covers \LBausch\CephRadosgwAdmin\ApiRequest::delete
     * @covers \LBausch\CephRadosgwAdmin\ApiRequest::make
     * @covers \LBausch\CephRadosgwAdmin\ApiRequest::request
     * @covers \LBausch\CephRadosgwAdmin\ApiResponse::__construct
     * @covers \LBausch\CephRadosgwAdmin\ApiResponse::get
     * @covers \LBausch\CephRadosgwAdmin\ApiResponse::failed
     * @covers \LBausch\CephRadosgwAdmin\ApiResponse::fromResponse
     * @covers \LBausch\CephRadosgwAdmin\ApiResponse::getResponse
     * @covers \LBausch\CephRadosgwAdmin\ApiResponse::has
     * @covers \LBausch\CephRadosgwAdmin\ApiResponse::shouldThrowException
     * @covers \LBausch\CephRadosgwAdmin\ApiResponse::succeeded
     * @covers \LBausch\CephRadosgwAdmin\Client
     * @covers \LBausch\CephRadosgwAdmin\Config
     * @covers \LBausch\CephRadosgwAdmin\Middlewares\SignatureMiddleware
     * @covers \LBausch\CephRadosgwAdmin\Resources\AbstractResource
     * @covers \LBausch\CephRadosgwAdmin\Resources\User
     * @covers \LBausch\CephRadosgwAdmin\Signature\SignatureV4
     */
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
        $client->user()->delete('foobar');
    }
}
