<?php

namespace Tests\Resources;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use LBausch\CephRadosgwAdmin\Client;
use Tests\TestCase;

final class UserTest extends TestCase
{
    /**
     * @covers \LBausch\CephRadosgwAdmin\ApiRequest
     * @covers \LBausch\CephRadosgwAdmin\ApiResponse
     * @covers \LBausch\CephRadosgwAdmin\Client
     * @covers \LBausch\CephRadosgwAdmin\Config
     * @covers \LBausch\CephRadosgwAdmin\Middlewares\SignatureMiddleware
     * @covers \LBausch\CephRadosgwAdmin\Resources\AbstractResource
     * @covers \LBausch\CephRadosgwAdmin\Resources\User::list
     * @covers \LBausch\CephRadosgwAdmin\Signature\SignatureV4::signRequest
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
        $this->assertEquals('http://gateway/admin/metadata/user', $request->getUri());
    }

    /**
     * @covers \LBausch\CephRadosgwAdmin\ApiRequest
     * @covers \LBausch\CephRadosgwAdmin\ApiResponse
     * @covers \LBausch\CephRadosgwAdmin\Client
     * @covers \LBausch\CephRadosgwAdmin\Config
     * @covers \LBausch\CephRadosgwAdmin\Middlewares\SignatureMiddleware
     * @covers \LBausch\CephRadosgwAdmin\Resources\AbstractResource
     * @covers \LBausch\CephRadosgwAdmin\Resources\User::info
     * @covers \LBausch\CephRadosgwAdmin\Signature\SignatureV4::signRequest
     */
    public function testUserInfoIsReturned(): void
    {
        $transactions = [];

        $config = $this->getConfigWithMockedHandlers($transactions, [
            new Response(200, [], <<<'EOT'
{
    "tenant": "foo",
    "user_id": "bar",
    "display_name": "foobar"
}
EOT),
        ]);

        $client = Client::make('http://gateway', 'acesskey', 'secretkey', $config);

        $response = $client->user()->info('foo$bar');

        $this->assertEquals([
            'tenant' => 'foo',
            'user_id' => 'bar',
            'display_name' => 'foobar',
        ], $response->get());

        $this->assertCount(1, $transactions);

        /** @var Request $request */
        $request = $transactions[0]['request'];

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('http://gateway/admin/user?uid=foo%24bar', $request->getUri());
    }

    /**
     * @covers \LBausch\CephRadosgwAdmin\ApiRequest
     * @covers \LBausch\CephRadosgwAdmin\ApiResponse
     * @covers \LBausch\CephRadosgwAdmin\Client
     * @covers \LBausch\CephRadosgwAdmin\Config
     * @covers \LBausch\CephRadosgwAdmin\Middlewares\SignatureMiddleware
     * @covers \LBausch\CephRadosgwAdmin\Resources\AbstractResource
     * @covers \LBausch\CephRadosgwAdmin\Resources\User::create
     * @covers \LBausch\CephRadosgwAdmin\Signature\SignatureV2::canonicalizedAmzHeaders
     * @covers \LBausch\CephRadosgwAdmin\Signature\SignatureV2::canonicalizedResource
     * @covers \LBausch\CephRadosgwAdmin\Signature\SignatureV2::contentMd5
     * @covers \LBausch\CephRadosgwAdmin\Signature\SignatureV2::expires
     * @covers \LBausch\CephRadosgwAdmin\Signature\SignatureV2::signRequest
     * @covers \LBausch\CephRadosgwAdmin\Signature\SignatureV2::stringToSign
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
        $this->assertEquals('http://gateway/admin/user?uid=foo&display-name=foo%20bar', $request->getUri());
    }

    /**
     * @covers \LBausch\CephRadosgwAdmin\ApiRequest
     * @covers \LBausch\CephRadosgwAdmin\ApiResponse
     * @covers \LBausch\CephRadosgwAdmin\Client
     * @covers \LBausch\CephRadosgwAdmin\Config
     * @covers \LBausch\CephRadosgwAdmin\Middlewares\SignatureMiddleware
     * @covers \LBausch\CephRadosgwAdmin\Resources\AbstractResource
     * @covers \LBausch\CephRadosgwAdmin\Resources\User::modify
     * @covers \LBausch\CephRadosgwAdmin\Signature\SignatureV4::signRequest
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
        $this->assertEquals('http://gateway/admin/user?uid=foo&display-name=baz', $request->getUri());
    }

    /**
     * @covers \LBausch\CephRadosgwAdmin\ApiRequest
     * @covers \LBausch\CephRadosgwAdmin\ApiResponse
     * @covers \LBausch\CephRadosgwAdmin\Client
     * @covers \LBausch\CephRadosgwAdmin\Config
     * @covers \LBausch\CephRadosgwAdmin\Middlewares\SignatureMiddleware
     * @covers \LBausch\CephRadosgwAdmin\Resources\AbstractResource
     * @covers \LBausch\CephRadosgwAdmin\Resources\User::createKey
     * @covers \LBausch\CephRadosgwAdmin\Signature\SignatureV4::signRequest
     */
    public function testKeyIsCreated()
    {
        $transactions = [];

        $config = $this->getConfigWithMockedHandlers($transactions, [
            new Response(),
        ]);

        $client = Client::make('http://gateway', 'acesskey', 'secretkey', $config);

        $response = $client->user()->createKey('foo');

        $this->assertEquals('', $response->get());

        $this->assertCount(1, $transactions);

        /** @var Request $request */
        $request = $transactions[0]['request'];

        $this->assertEquals('PUT', $request->getMethod());
        $this->assertEquals('http://gateway/admin/user?key=&uid=foo', $request->getUri());
    }

    /**
     * @covers \LBausch\CephRadosgwAdmin\ApiRequest
     * @covers \LBausch\CephRadosgwAdmin\ApiResponse
     * @covers \LBausch\CephRadosgwAdmin\Client
     * @covers \LBausch\CephRadosgwAdmin\Config
     * @covers \LBausch\CephRadosgwAdmin\Middlewares\SignatureMiddleware
     * @covers \LBausch\CephRadosgwAdmin\Resources\AbstractResource
     * @covers \LBausch\CephRadosgwAdmin\Resources\User::deleteKey
     * @covers \LBausch\CephRadosgwAdmin\Signature\SignatureV4::signRequest
     */
    public function testKeyIsDeleted()
    {
        $transactions = [];

        $config = $this->getConfigWithMockedHandlers($transactions, [
            new Response(),
        ]);

        $client = Client::make('http://gateway', 'acesskey', 'secretkey', $config);

        $response = $client->user()->deleteKey('access key');

        $this->assertEquals('', $response->get());

        $this->assertCount(1, $transactions);

        /** @var Request $request */
        $request = $transactions[0]['request'];

        $this->assertEquals('DELETE', $request->getMethod());
        $this->assertEquals('http://gateway/admin/user?key=&access-key=access%20key', $request->getUri());
    }

    /**
     * @covers \LBausch\CephRadosgwAdmin\ApiRequest
     * @covers \LBausch\CephRadosgwAdmin\ApiResponse
     * @covers \LBausch\CephRadosgwAdmin\Client
     * @covers \LBausch\CephRadosgwAdmin\Config
     * @covers \LBausch\CephRadosgwAdmin\Middlewares\SignatureMiddleware
     * @covers \LBausch\CephRadosgwAdmin\Resources\AbstractResource
     * @covers \LBausch\CephRadosgwAdmin\Resources\User::createSubuser
     * @covers \LBausch\CephRadosgwAdmin\Signature\SignatureV4::signRequest
     */
    public function testSubuserIsCreated()
    {
        $transactions = [];

        $config = $this->getConfigWithMockedHandlers($transactions, [
            new Response(200, [], '[{"id":"foo:bar","permissions":"<none>"}]'),
        ]);

        $client = Client::make('http://gateway', 'acesskey', 'secretkey', $config);

        $response = $client->user()->createSubuser('foo', 'bar');

        $this->assertEquals([
            [
                'id' => 'foo:bar',
                'permissions' => '<none>',
            ],
        ], $response->get());

        $this->assertCount(1, $transactions);

        /** @var Request $request */
        $request = $transactions[0]['request'];

        $this->assertEquals('PUT', $request->getMethod());
        $this->assertEquals('http://gateway/admin/user?uid=foo&subuser=bar', $request->getUri());
    }

    /**
     * @covers \LBausch\CephRadosgwAdmin\ApiRequest
     * @covers \LBausch\CephRadosgwAdmin\ApiResponse
     * @covers \LBausch\CephRadosgwAdmin\Client
     * @covers \LBausch\CephRadosgwAdmin\Config
     * @covers \LBausch\CephRadosgwAdmin\Middlewares\SignatureMiddleware
     * @covers \LBausch\CephRadosgwAdmin\Resources\AbstractResource
     * @covers \LBausch\CephRadosgwAdmin\Resources\User::modifySubuser
     * @covers \LBausch\CephRadosgwAdmin\Signature\SignatureV4::signRequest
     */
    public function testSubuserIsModified()
    {
        $transactions = [];

        $config = $this->getConfigWithMockedHandlers($transactions, [
            new Response(200, [], '[{"id":"foo:bar","permissions":"read"}]'),
        ]);

        $client = Client::make('http://gateway', 'acesskey', 'secretkey', $config);

        $response = $client->user()->modifySubuser('foo', 'bar', ['access' => 'read']);

        $this->assertEquals([
            [
                'id' => 'foo:bar',
                'permissions' => 'read',
            ],
        ], $response->get());

        $this->assertCount(1, $transactions);

        /** @var Request $request */
        $request = $transactions[0]['request'];

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('http://gateway/admin/user?uid=foo&subuser=bar&access=read', $request->getUri());
    }

    /**
     * @covers \LBausch\CephRadosgwAdmin\ApiRequest
     * @covers \LBausch\CephRadosgwAdmin\ApiResponse
     * @covers \LBausch\CephRadosgwAdmin\Client
     * @covers \LBausch\CephRadosgwAdmin\Config
     * @covers \LBausch\CephRadosgwAdmin\Middlewares\SignatureMiddleware
     * @covers \LBausch\CephRadosgwAdmin\Resources\AbstractResource
     * @covers \LBausch\CephRadosgwAdmin\Resources\User::deleteSubuser
     * @covers \LBausch\CephRadosgwAdmin\Signature\SignatureV4::signRequest
     */
    public function testSubuserIsDeleted()
    {
        $transactions = [];

        $config = $this->getConfigWithMockedHandlers($transactions, [
            new Response(),
        ]);

        $client = Client::make('http://gateway', 'acesskey', 'secretkey', $config);

        $response = $client->user()->deleteSubuser('foo', 'bar');

        $this->assertEquals('', $response->get());

        $this->assertCount(1, $transactions);

        /** @var Request $request */
        $request = $transactions[0]['request'];

        $this->assertEquals('DELETE', $request->getMethod());
        $this->assertEquals('http://gateway/admin/user?uid=foo&subuser=bar', $request->getUri());
    }

    /**
     * @covers \LBausch\CephRadosgwAdmin\ApiRequest
     * @covers \LBausch\CephRadosgwAdmin\ApiResponse
     * @covers \LBausch\CephRadosgwAdmin\Client
     * @covers \LBausch\CephRadosgwAdmin\Config
     * @covers \LBausch\CephRadosgwAdmin\Middlewares\SignatureMiddleware
     * @covers \LBausch\CephRadosgwAdmin\Resources\AbstractResource
     * @covers \LBausch\CephRadosgwAdmin\Resources\User::addCapability
     * @covers \LBausch\CephRadosgwAdmin\Signature\SignatureV4::signRequest
     */
    public function testCapabilityIsAdded()
    {
        $transactions = [];

        $config = $this->getConfigWithMockedHandlers($transactions, [
            new Response(200, [], '[{"type":"usage","perm":"read"}]'),
        ]);

        $client = Client::make('http://gateway', 'acesskey', 'secretkey', $config);

        $response = $client->user()->addCapability('foo', 'usage=read');

        $this->assertEquals([
            [
                'type' => 'usage',
                'perm' => 'read',
            ],
        ], $response->get());

        $this->assertCount(1, $transactions);

        /** @var Request $request */
        $request = $transactions[0]['request'];

        $this->assertEquals('PUT', $request->getMethod());
        $this->assertEquals('http://gateway/admin/user?caps=&uid=foo&user-caps=usage%3Dread', $request->getUri());
    }

    /**
     * @covers \LBausch\CephRadosgwAdmin\ApiRequest
     * @covers \LBausch\CephRadosgwAdmin\ApiResponse
     * @covers \LBausch\CephRadosgwAdmin\Client
     * @covers \LBausch\CephRadosgwAdmin\Config
     * @covers \LBausch\CephRadosgwAdmin\Middlewares\SignatureMiddleware
     * @covers \LBausch\CephRadosgwAdmin\Resources\AbstractResource
     * @covers \LBausch\CephRadosgwAdmin\Resources\User::deleteCapability
     * @covers \LBausch\CephRadosgwAdmin\Signature\SignatureV4::signRequest
     */
    public function testCapabilityIsDeleted()
    {
        $transactions = [];

        $config = $this->getConfigWithMockedHandlers($transactions, [
            new Response(200, [], '[]'),
        ]);

        $client = Client::make('http://gateway', 'acesskey', 'secretkey', $config);

        $response = $client->user()->deleteCapability('foo', 'usage=read');

        $this->assertEquals([], $response->get());

        $this->assertCount(1, $transactions);

        /** @var Request $request */
        $request = $transactions[0]['request'];

        $this->assertEquals('DELETE', $request->getMethod());
        $this->assertEquals('http://gateway/admin/user?caps=&uid=foo&user-caps=usage%3Dread', $request->getUri());
    }
}
