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

        $this->assertSame(['foobar'], $response->get());

        $this->assertCount(1, $transactions);

        /** @var Request $request */
        $request = $transactions[0]['request'];

        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('http://gateway/admin/metadata/user', (string) $request->getUri());
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

        $this->assertSame([
            'tenant' => 'foo',
            'user_id' => 'bar',
            'display_name' => 'foobar',
        ], $response->get());

        $this->assertCount(1, $transactions);

        /** @var Request $request */
        $request = $transactions[0]['request'];

        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('http://gateway/admin/user?uid=foo%24bar', (string) $request->getUri());
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

        $this->assertSame([
            'tenant' => '',
            'user_id' => 'foo',
            'display_name' => 'foo bar',
        ], $response->get());

        $this->assertCount(1, $transactions);

        /** @var Request $request */
        $request = $transactions[0]['request'];

        $this->assertSame('PUT', $request->getMethod());
        $this->assertSame('http://gateway/admin/user?uid=foo&display-name=foo%20bar', (string) $request->getUri());
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

        $this->assertSame([
            'tenant' => '',
            'user_id' => 'foo',
            'display_name' => 'baz',
        ], $response->get());

        $this->assertCount(1, $transactions);

        /** @var Request $request */
        $request = $transactions[0]['request'];

        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('http://gateway/admin/user?uid=foo&display-name=baz', (string) $request->getUri());
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
    public function testKeyIsCreated(): void
    {
        $transactions = [];

        $config = $this->getConfigWithMockedHandlers($transactions, [
            new Response(),
        ]);

        $client = Client::make('http://gateway', 'acesskey', 'secretkey', $config);

        $response = $client->user()->createKey('foo');

        $this->assertNull($response->get());

        $this->assertCount(1, $transactions);

        /** @var Request $request */
        $request = $transactions[0]['request'];

        $this->assertSame('PUT', $request->getMethod());
        $this->assertSame('http://gateway/admin/user?key=&uid=foo', (string) $request->getUri());
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
    public function testKeyIsDeleted(): void
    {
        $transactions = [];

        $config = $this->getConfigWithMockedHandlers($transactions, [
            new Response(),
        ]);

        $client = Client::make('http://gateway', 'acesskey', 'secretkey', $config);

        $response = $client->user()->deleteKey('access key');

        $this->assertNull($response->get());

        $this->assertCount(1, $transactions);

        /** @var Request $request */
        $request = $transactions[0]['request'];

        $this->assertSame('DELETE', $request->getMethod());
        $this->assertSame('http://gateway/admin/user?key=&access-key=access%20key', (string) $request->getUri());
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
    public function testSubuserIsCreated(): void
    {
        $transactions = [];

        $config = $this->getConfigWithMockedHandlers($transactions, [
            new Response(200, [], '[{"id":"foo:bar","permissions":"<none>"}]'),
        ]);

        $client = Client::make('http://gateway', 'acesskey', 'secretkey', $config);

        $response = $client->user()->createSubuser('foo', 'bar');

        $this->assertSame([
            [
                'id' => 'foo:bar',
                'permissions' => '<none>',
            ],
        ], $response->get());

        $this->assertCount(1, $transactions);

        /** @var Request $request */
        $request = $transactions[0]['request'];

        $this->assertSame('PUT', $request->getMethod());
        $this->assertSame('http://gateway/admin/user?uid=foo&subuser=bar', (string) $request->getUri());
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
    public function testSubuserIsModified(): void
    {
        $transactions = [];

        $config = $this->getConfigWithMockedHandlers($transactions, [
            new Response(200, [], '[{"id":"foo:bar","permissions":"read"}]'),
        ]);

        $client = Client::make('http://gateway', 'acesskey', 'secretkey', $config);

        $response = $client->user()->modifySubuser('foo', 'bar', ['access' => 'read']);

        $this->assertSame([
            [
                'id' => 'foo:bar',
                'permissions' => 'read',
            ],
        ], $response->get());

        $this->assertCount(1, $transactions);

        /** @var Request $request */
        $request = $transactions[0]['request'];

        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('http://gateway/admin/user?uid=foo&subuser=bar&access=read', (string) $request->getUri());
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
    public function testSubuserIsDeleted(): void
    {
        $transactions = [];

        $config = $this->getConfigWithMockedHandlers($transactions, [
            new Response(),
        ]);

        $client = Client::make('http://gateway', 'acesskey', 'secretkey', $config);

        $response = $client->user()->deleteSubuser('foo', 'bar');

        $this->assertNull($response->get());

        $this->assertCount(1, $transactions);

        /** @var Request $request */
        $request = $transactions[0]['request'];

        $this->assertSame('DELETE', $request->getMethod());
        $this->assertSame('http://gateway/admin/user?uid=foo&subuser=bar', (string) $request->getUri());
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
    public function testCapabilityIsAdded(): void
    {
        $transactions = [];

        $config = $this->getConfigWithMockedHandlers($transactions, [
            new Response(200, [], '[{"type":"usage","perm":"read"}]'),
        ]);

        $client = Client::make('http://gateway', 'acesskey', 'secretkey', $config);

        $response = $client->user()->addCapability('foo', 'usage=read');

        $this->assertSame([
            [
                'type' => 'usage',
                'perm' => 'read',
            ],
        ], $response->get());

        $this->assertCount(1, $transactions);

        /** @var Request $request */
        $request = $transactions[0]['request'];

        $this->assertSame('PUT', $request->getMethod());
        $this->assertSame('http://gateway/admin/user?caps=&uid=foo&user-caps=usage%3Dread', (string) $request->getUri());
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
    public function testCapabilityIsDeleted(): void
    {
        $transactions = [];

        $config = $this->getConfigWithMockedHandlers($transactions, [
            new Response(200, [], '[]'),
        ]);

        $client = Client::make('http://gateway', 'acesskey', 'secretkey', $config);

        $response = $client->user()->deleteCapability('foo', 'usage=read');

        $this->assertSame([], $response->get());

        $this->assertCount(1, $transactions);

        /** @var Request $request */
        $request = $transactions[0]['request'];

        $this->assertSame('DELETE', $request->getMethod());
        $this->assertSame('http://gateway/admin/user?caps=&uid=foo&user-caps=usage%3Dread', (string) $request->getUri());
    }

    /**
     * @covers \LBausch\CephRadosgwAdmin\ApiRequest
     * @covers \LBausch\CephRadosgwAdmin\ApiResponse
     * @covers \LBausch\CephRadosgwAdmin\Client
     * @covers \LBausch\CephRadosgwAdmin\Config
     * @covers \LBausch\CephRadosgwAdmin\Middlewares\SignatureMiddleware
     * @covers \LBausch\CephRadosgwAdmin\Resources\AbstractResource
     * @covers \LBausch\CephRadosgwAdmin\Resources\User::getQuota
     * @covers \LBausch\CephRadosgwAdmin\Signature\SignatureV4::signRequest
     */
    public function testUserQuotaIsReturned(): void
    {
        $transactions = [];

        $config = $this->getConfigWithMockedHandlers($transactions, [
            new Response(200, [], '{"enabled":false,"check_on_raw":false,"max_size":-1,"max_size_kb":0,"max_objects":-1}'),
        ]);

        $client = Client::make('http://gateway', 'acesskey', 'secretkey', $config);

        $response = $client->user()->getQuota('foo');

        $this->assertSame([
            'enabled' => false,
            'check_on_raw' => false,
            'max_size' => -1,
            'max_size_kb' => 0,
            'max_objects' => -1,
        ], $response->get());

        $this->assertCount(1, $transactions);

        /** @var Request $request */
        $request = $transactions[0]['request'];

        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('http://gateway/admin/user?quota=&uid=foo&quota-type=user', (string) $request->getUri());
    }

    /**
     * @covers \LBausch\CephRadosgwAdmin\ApiRequest
     * @covers \LBausch\CephRadosgwAdmin\ApiResponse
     * @covers \LBausch\CephRadosgwAdmin\Client
     * @covers \LBausch\CephRadosgwAdmin\Config
     * @covers \LBausch\CephRadosgwAdmin\Middlewares\SignatureMiddleware
     * @covers \LBausch\CephRadosgwAdmin\Resources\AbstractResource
     * @covers \LBausch\CephRadosgwAdmin\Resources\User::setQuota
     * @covers \LBausch\CephRadosgwAdmin\Signature\SignatureV2
     */
    public function testUserQuotaIsSet(): void
    {
        $transactions = [];

        $config = $this->getConfigWithMockedHandlers($transactions, [
            new Response(),
        ]);

        $client = Client::make('http://gateway', 'acesskey', 'secretkey', $config);

        $response = $client->user()->setQuota('foo', ['enabled' => true]);

        $this->assertNull($response->get());

        $this->assertCount(1, $transactions);

        /** @var Request $request */
        $request = $transactions[0]['request'];

        $this->assertSame('PUT', $request->getMethod());
        $this->assertSame('http://gateway/admin/user?quota=&uid=foo&quota-type=user', (string) $request->getUri());
    }

    /**
     * @covers \LBausch\CephRadosgwAdmin\ApiRequest
     * @covers \LBausch\CephRadosgwAdmin\ApiResponse
     * @covers \LBausch\CephRadosgwAdmin\Client
     * @covers \LBausch\CephRadosgwAdmin\Config
     * @covers \LBausch\CephRadosgwAdmin\Middlewares\SignatureMiddleware
     * @covers \LBausch\CephRadosgwAdmin\Resources\AbstractResource
     * @covers \LBausch\CephRadosgwAdmin\Resources\User::getBucketQuota
     * @covers \LBausch\CephRadosgwAdmin\Signature\SignatureV4::signRequest
     */
    public function testBucketQuotaIsReturned(): void
    {
        $transactions = [];

        $config = $this->getConfigWithMockedHandlers($transactions, [
            new Response(200, [], '{"enabled":false,"check_on_raw":false,"max_size":-1,"max_size_kb":0,"max_objects":-1}'),
        ]);

        $client = Client::make('http://gateway', 'acesskey', 'secretkey', $config);

        $response = $client->user()->getBucketQuota('foo');

        $this->assertSame([
            'enabled' => false,
            'check_on_raw' => false,
            'max_size' => -1,
            'max_size_kb' => 0,
            'max_objects' => -1,
        ], $response->get());

        $this->assertCount(1, $transactions);

        /** @var Request $request */
        $request = $transactions[0]['request'];

        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('http://gateway/admin/user?quota=&uid=foo&quota-type=bucket', (string) $request->getUri());
    }

    /**
     * @covers \LBausch\CephRadosgwAdmin\ApiRequest
     * @covers \LBausch\CephRadosgwAdmin\ApiResponse
     * @covers \LBausch\CephRadosgwAdmin\Client
     * @covers \LBausch\CephRadosgwAdmin\Config
     * @covers \LBausch\CephRadosgwAdmin\Middlewares\SignatureMiddleware
     * @covers \LBausch\CephRadosgwAdmin\Resources\AbstractResource
     * @covers \LBausch\CephRadosgwAdmin\Resources\User::setBucketQuota
     * @covers \LBausch\CephRadosgwAdmin\Signature\SignatureV2
     */
    public function testBucketQuotaIsSet(): void
    {
        $transactions = [];

        $config = $this->getConfigWithMockedHandlers($transactions, [
            new Response(),
        ]);

        $client = Client::make('http://gateway', 'acesskey', 'secretkey', $config);

        $response = $client->user()->setBucketQuota('foo', ['enabled' => true]);

        $this->assertNull($response->get());

        $this->assertCount(1, $transactions);

        /** @var Request $request */
        $request = $transactions[0]['request'];

        $this->assertSame('PUT', $request->getMethod());
        $this->assertSame('http://gateway/admin/user?quota=&uid=foo&quota-type=bucket', (string) $request->getUri());
    }
}
