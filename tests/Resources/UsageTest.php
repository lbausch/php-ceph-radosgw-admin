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
#[CoversMethod(\LBausch\CephRadosgwAdmin\Resources\Usage::class, 'info')]
#[CoversMethod(\LBausch\CephRadosgwAdmin\Resources\Usage::class, 'trim')]
final class UsageTest extends TestCase
{
    public function testUsageInfoIsReturned(): void
    {
        $transactions = [];

        $config = $this->getConfigWithMockedHandlers($transactions, [
            new Response(200, [], '{"entries":[]}'),
        ]);

        $client = Client::make('http://gateway', 'acesskey', 'secretkey', $config);

        $response = $client->usage()->info();

        $this->assertSame([
            'entries' => [],
        ], $response->get());

        $this->assertCount(1, $transactions);

        /** @var Request $request */
        $request = $transactions[0]['request'];

        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('http://gateway/admin/usage', (string) $request->getUri());
    }

    public function testUsageIsTrimmed(): void
    {
        $transactions = [];

        $config = $this->getConfigWithMockedHandlers($transactions, [
            new Response(),
        ]);

        $client = Client::make('http://gateway', 'acesskey', 'secretkey', $config);

        $response = $client->usage()->trim([
            'uid' => 'foo',
            'start' => '1970-01-01 13:37:00',
        ]);

        $this->assertNull($response->get());

        $this->assertCount(1, $transactions);

        /** @var Request $request */
        $request = $transactions[0]['request'];

        $this->assertSame('DELETE', $request->getMethod());
        $this->assertSame('http://gateway/admin/usage?uid=foo&start=1970-01-01%2013%3A37%3A00', (string) $request->getUri());
    }
}
