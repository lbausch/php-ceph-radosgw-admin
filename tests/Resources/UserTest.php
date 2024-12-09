<?php

namespace Tests\Resources;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use LBausch\CephRadosgwAdmin\Client;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use Tests\TestCase;

#[CoversClass(\LBausch\CephRadosgwAdmin\ApiRequest::class)]
#[CoversClass(\LBausch\CephRadosgwAdmin\ApiResponse::class)]
#[CoversClass(Client::class)]
#[CoversClass(\LBausch\CephRadosgwAdmin\Config::class)]
#[CoversClass(\LBausch\CephRadosgwAdmin\Signature\SignatureV2::class)]
#[CoversClass(\LBausch\CephRadosgwAdmin\Signature\SignatureV4::class)]
#[CoversClass(\LBausch\CephRadosgwAdmin\Middlewares\SignatureMiddleware::class)]
#[CoversClass(\LBausch\CephRadosgwAdmin\Resources\AbstractResource::class)]
#[CoversMethod(\LBausch\CephRadosgwAdmin\Resources\User::class, 'addCapability')]
#[CoversMethod(\LBausch\CephRadosgwAdmin\Resources\User::class, 'create')]
#[CoversMethod(\LBausch\CephRadosgwAdmin\Resources\User::class, 'createKey')]
#[CoversMethod(\LBausch\CephRadosgwAdmin\Resources\User::class, 'createSubuser')]
#[CoversMethod(\LBausch\CephRadosgwAdmin\Resources\User::class, 'getBucketQuota')]
#[CoversMethod(\LBausch\CephRadosgwAdmin\Resources\User::class, 'getQuota')]
#[CoversMethod(\LBausch\CephRadosgwAdmin\Resources\User::class, 'info')]
#[CoversMethod(\LBausch\CephRadosgwAdmin\Resources\User::class, 'list')]
#[CoversMethod(\LBausch\CephRadosgwAdmin\Resources\User::class, 'modify')]
#[CoversMethod(\LBausch\CephRadosgwAdmin\Resources\User::class, 'modifySubuser')]
#[CoversMethod(\LBausch\CephRadosgwAdmin\Resources\User::class, 'removeCapability')]
#[CoversMethod(\LBausch\CephRadosgwAdmin\Resources\User::class, 'removeKey')]
#[CoversMethod(\LBausch\CephRadosgwAdmin\Resources\User::class, 'removeSubuser')]
#[CoversMethod(\LBausch\CephRadosgwAdmin\Resources\User::class, 'setBucketQuota')]
#[CoversMethod(\LBausch\CephRadosgwAdmin\Resources\User::class, 'setQuota')]
final class UserTest extends TestCase
{
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

    public function testKeyIsRemoved(): void
    {
        $transactions = [];

        $config = $this->getConfigWithMockedHandlers($transactions, [
            new Response(),
        ]);

        $client = Client::make('http://gateway', 'acesskey', 'secretkey', $config);

        $response = $client->user()->removeKey('access key');

        $this->assertNull($response->get());

        $this->assertCount(1, $transactions);

        /** @var Request $request */
        $request = $transactions[0]['request'];

        $this->assertSame('DELETE', $request->getMethod());
        $this->assertSame('http://gateway/admin/user?key=&access-key=access%20key', (string) $request->getUri());
    }

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

    public function testSubuserIsRemoved(): void
    {
        $transactions = [];

        $config = $this->getConfigWithMockedHandlers($transactions, [
            new Response(),
        ]);

        $client = Client::make('http://gateway', 'acesskey', 'secretkey', $config);

        $response = $client->user()->removeSubuser('foo', 'bar');

        $this->assertNull($response->get());

        $this->assertCount(1, $transactions);

        /** @var Request $request */
        $request = $transactions[0]['request'];

        $this->assertSame('DELETE', $request->getMethod());
        $this->assertSame('http://gateway/admin/user?uid=foo&subuser=bar', (string) $request->getUri());
    }

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

    public function testCapabilityIsRemoved(): void
    {
        $transactions = [];

        $config = $this->getConfigWithMockedHandlers($transactions, [
            new Response(200, [], '[]'),
        ]);

        $client = Client::make('http://gateway', 'acesskey', 'secretkey', $config);

        $response = $client->user()->removeCapability('foo', 'usage=read');

        $this->assertSame([], $response->get());

        $this->assertCount(1, $transactions);

        /** @var Request $request */
        $request = $transactions[0]['request'];

        $this->assertSame('DELETE', $request->getMethod());
        $this->assertSame('http://gateway/admin/user?caps=&uid=foo&user-caps=usage%3Dread', (string) $request->getUri());
    }

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
