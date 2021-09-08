<?php

namespace Tests\Resources;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use LBausch\CephRadosgwAdmin\Client;
use Tests\TestCase;

final class UsageTest extends TestCase
{
    /**
     * @covers \LBausch\CephRadosgwAdmin\ApiRequest
     * @covers \LBausch\CephRadosgwAdmin\ApiResponse
     * @covers \LBausch\CephRadosgwAdmin\Client
     * @covers \LBausch\CephRadosgwAdmin\Config
     * @covers \LBausch\CephRadosgwAdmin\Middlewares\SignatureMiddleware
     * @covers \LBausch\CephRadosgwAdmin\Resources\AbstractResource
     * @covers \LBausch\CephRadosgwAdmin\Resources\Usage::info
     * @covers \LBausch\CephRadosgwAdmin\Signature\SignatureV4::signRequest
     */
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

    /**
     * @covers \LBausch\CephRadosgwAdmin\ApiRequest
     * @covers \LBausch\CephRadosgwAdmin\ApiResponse
     * @covers \LBausch\CephRadosgwAdmin\Client
     * @covers \LBausch\CephRadosgwAdmin\Config
     * @covers \LBausch\CephRadosgwAdmin\Middlewares\SignatureMiddleware
     * @covers \LBausch\CephRadosgwAdmin\Resources\AbstractResource
     * @covers \LBausch\CephRadosgwAdmin\Resources\Usage::trim
     * @covers \LBausch\CephRadosgwAdmin\Signature\SignatureV4::signRequest
     */
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
