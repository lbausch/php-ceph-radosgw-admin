<?php

namespace Tests\Resources;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use LBausch\PhpRadosgwAdmin\Client;
use Tests\TestCase;

final class UsageTest extends TestCase
{
    /**
     * @covers \LBausch\PhpRadosgwAdmin\ApiRequest
     * @covers \LBausch\PhpRadosgwAdmin\ApiResponse
     * @covers \LBausch\PhpRadosgwAdmin\Client
     * @covers \LBausch\PhpRadosgwAdmin\Config
     * @covers \LBausch\PhpRadosgwAdmin\Middlewares\SignatureMiddleware
     * @covers \LBausch\PhpRadosgwAdmin\Resources\AbstractResource
     * @covers \LBausch\PhpRadosgwAdmin\Resources\Usage::info
     * @covers \LBausch\PhpRadosgwAdmin\Signature\SignatureV4::signRequest
     */
    public function testUsageInfoIsReturned(): void
    {
        $transactions = [];

        $config = $this->getConfigWithMockedHandlers($transactions, [
            new Response(200, [], '[]'),
        ]);

        $client = Client::make('http://gateway', 'acesskey', 'secretkey', $config);

        $response = $client->usage()->info();

        $this->assertEquals([], $response->get());

        $this->assertCount(1, $transactions);

        /** @var Request $request */
        $request = $transactions[0]['request'];

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('gateway', $request->getUri()->getHost());
        $this->assertEquals('/admin/usage', $request->getUri()->getPath());
    }
}
