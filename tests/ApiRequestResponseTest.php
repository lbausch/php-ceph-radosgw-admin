<?php

namespace Tests;

use GuzzleHttp\Psr7\Response;
use LBausch\PhpRadosgwAdmin\ApiException;
use LBausch\PhpRadosgwAdmin\Client;

final class RequestResponseTest extends TestCase
{
    /**
     * @covers \LBausch\PhpRadosgwAdmin\ApiRequest::__construct
     * @covers \LBausch\PhpRadosgwAdmin\ApiRequest::get
     * @covers \LBausch\PhpRadosgwAdmin\ApiRequest::make
     * @covers \LBausch\PhpRadosgwAdmin\ApiRequest::request
     * @covers \LBausch\PhpRadosgwAdmin\ApiResponse::__construct
     * @covers \LBausch\PhpRadosgwAdmin\ApiResponse::failed
     * @covers \LBausch\PhpRadosgwAdmin\ApiResponse::fromResponse
     * @covers \LBausch\PhpRadosgwAdmin\ApiResponse::get
     * @covers \LBausch\PhpRadosgwAdmin\ApiResponse::getResponse
     * @covers \LBausch\PhpRadosgwAdmin\ApiResponse::has
     * @covers \LBausch\PhpRadosgwAdmin\ApiResponse::shouldThrowException
     * @covers \LBausch\PhpRadosgwAdmin\ApiResponse::succeeded
     * @covers \LBausch\PhpRadosgwAdmin\Client
     * @covers \LBausch\PhpRadosgwAdmin\Config
     * @covers \LBausch\PhpRadosgwAdmin\Middlewares\SignatureMiddleware
     * @covers \LBausch\PhpRadosgwAdmin\Resources\AbstractResource
     * @covers \LBausch\PhpRadosgwAdmin\Resources\User
     * @covers \LBausch\PhpRadosgwAdmin\Signature\SignatureV4
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
     * @covers \LBausch\PhpRadosgwAdmin\ApiRequest::__construct
     * @covers \LBausch\PhpRadosgwAdmin\ApiRequest::delete
     * @covers \LBausch\PhpRadosgwAdmin\ApiRequest::make
     * @covers \LBausch\PhpRadosgwAdmin\ApiRequest::request
     * @covers \LBausch\PhpRadosgwAdmin\ApiResponse::__construct
     * @covers \LBausch\PhpRadosgwAdmin\ApiResponse::get
     * @covers \LBausch\PhpRadosgwAdmin\ApiResponse::failed
     * @covers \LBausch\PhpRadosgwAdmin\ApiResponse::fromResponse
     * @covers \LBausch\PhpRadosgwAdmin\ApiResponse::getResponse
     * @covers \LBausch\PhpRadosgwAdmin\ApiResponse::has
     * @covers \LBausch\PhpRadosgwAdmin\ApiResponse::shouldThrowException
     * @covers \LBausch\PhpRadosgwAdmin\ApiResponse::succeeded
     * @covers \LBausch\PhpRadosgwAdmin\Client
     * @covers \LBausch\PhpRadosgwAdmin\Config
     * @covers \LBausch\PhpRadosgwAdmin\Middlewares\SignatureMiddleware
     * @covers \LBausch\PhpRadosgwAdmin\Resources\AbstractResource
     * @covers \LBausch\PhpRadosgwAdmin\Resources\User
     * @covers \LBausch\PhpRadosgwAdmin\Signature\SignatureV4
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
