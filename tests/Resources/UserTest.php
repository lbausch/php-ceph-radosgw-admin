<?php

namespace Tests\Resources;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use LBausch\PhpRadosgwAdmin\Client;
use Tests\TestCase;

final class UserTest extends TestCase
{
    /**
     * @covers \LBausch\PhpRadosgwAdmin\ApiRequest
     * @covers \LBausch\PhpRadosgwAdmin\ApiResponse
     * @covers \LBausch\PhpRadosgwAdmin\Client
     * @covers \LBausch\PhpRadosgwAdmin\Config
     * @covers \LBausch\PhpRadosgwAdmin\Middlewares\SignatureMiddleware
     * @covers \LBausch\PhpRadosgwAdmin\Resources\AbstractResource
     * @covers \LBausch\PhpRadosgwAdmin\Resources\User::list
     * @covers \LBausch\PhpRadosgwAdmin\Signature\SignatureV4::signRequest
     */
    public function testUsersAreListed(): void
    {
        $transactions = [];

        $config = $this->getConfigWithMockedHandlers($transactions, [
            new Response(200, [], '["foobar"]'),
        ]);

        $client = Client::make('http://gateway', 'acesskey', 'secretkey', $config);

        $response = $client->user()->list();

        $this->assertEquals(['foobar'], $response->get());

        $this->assertCount(1, $transactions);

        /** @var Request $request */
        $request = $transactions[0]['request'];

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('gateway', $request->getUri()->getHost());
        $this->assertEquals('/admin/metadata/user', $request->getUri()->getPath());
    }

    /**
     * @covers \LBausch\PhpRadosgwAdmin\ApiRequest
     * @covers \LBausch\PhpRadosgwAdmin\ApiResponse
     * @covers \LBausch\PhpRadosgwAdmin\Client
     * @covers \LBausch\PhpRadosgwAdmin\Config
     * @covers \LBausch\PhpRadosgwAdmin\Middlewares\SignatureMiddleware
     * @covers \LBausch\PhpRadosgwAdmin\Resources\AbstractResource
     * @covers \LBausch\PhpRadosgwAdmin\Resources\User::create
     * @covers \LBausch\PhpRadosgwAdmin\Signature\SignatureV2::canonicalizedAmzHeaders
     * @covers \LBausch\PhpRadosgwAdmin\Signature\SignatureV2::canonicalizedResource
     * @covers \LBausch\PhpRadosgwAdmin\Signature\SignatureV2::contentMd5
     * @covers \LBausch\PhpRadosgwAdmin\Signature\SignatureV2::expires
     * @covers \LBausch\PhpRadosgwAdmin\Signature\SignatureV2::signRequest
     * @covers \LBausch\PhpRadosgwAdmin\Signature\SignatureV2::stringToSign
     */
    public function testUserIsCreated(): void
    {
        $transactions = [];

        $config = $this->getConfigWithMockedHandlers($transactions, [
            new Response(200, [], '{"tenant":"","user_id":"foo","display_name":"foo bar"}'),
        ]);

        $client = Client::make('http://gateway', 'acesskey', 'secretkey', $config);

        $response = $client->user()->create('foo', 'foo bar');

        $this->assertEquals([
            'tenant' => '',
            'user_id' => 'foo',
            'display_name' => 'foo bar',
        ], $response->get());

        $this->assertCount(1, $transactions);

        /** @var Request $request */
        $request = $transactions[0]['request'];

        $this->assertEquals('PUT', $request->getMethod());
        $this->assertEquals('/admin/user', $request->getUri()->getPath());
        $this->assertEquals('uid=foo&display-name=foo%20bar', $request->getUri()->getQuery());
    }

    /**
     * @covers \LBausch\PhpRadosgwAdmin\ApiRequest
     * @covers \LBausch\PhpRadosgwAdmin\ApiResponse
     * @covers \LBausch\PhpRadosgwAdmin\Client
     * @covers \LBausch\PhpRadosgwAdmin\Config
     * @covers \LBausch\PhpRadosgwAdmin\Middlewares\SignatureMiddleware
     * @covers \LBausch\PhpRadosgwAdmin\Resources\AbstractResource
     * @covers \LBausch\PhpRadosgwAdmin\Resources\User::modify
     * @covers \LBausch\PhpRadosgwAdmin\Signature\SignatureV4::signRequest
     */
    public function testUserIsModified(): void
    {
        $transactions = [];

        $config = $this->getConfigWithMockedHandlers($transactions, [
            new Response(200, [], '{"tenant":"","user_id":"foo","display_name":"baz"}'),
        ]);

        $client = Client::make('http://gateway', 'acesskey', 'secretkey', $config);

        $response = $client->user()->modify('foo', ['display-name' => 'baz']);

        $this->assertEquals([
            'tenant' => '',
            'user_id' => 'foo',
            'display_name' => 'baz',
        ], $response->get());

        $this->assertCount(1, $transactions);

        /** @var Request $request */
        $request = $transactions[0]['request'];

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('/admin/user', $request->getUri()->getPath());
        $this->assertEquals('uid=foo&display-name=baz', $request->getUri()->getQuery());
    }
}
