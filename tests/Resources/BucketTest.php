<?php

namespace Tests\Resources;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use LBausch\PhpRadosgwAdmin\Client;
use Tests\TestCase;

final class BucketTest extends TestCase
{
    /**
     * @covers \LBausch\PhpRadosgwAdmin\ApiRequest
     * @covers \LBausch\PhpRadosgwAdmin\ApiResponse
     * @covers \LBausch\PhpRadosgwAdmin\Client
     * @covers \LBausch\PhpRadosgwAdmin\Config
     * @covers \LBausch\PhpRadosgwAdmin\Middlewares\SignatureMiddleware
     * @covers \LBausch\PhpRadosgwAdmin\Resources\AbstractResource
     * @covers \LBausch\PhpRadosgwAdmin\Resources\Bucket::list
     * @covers \LBausch\PhpRadosgwAdmin\Signature\SignatureV4::signRequest
     */
    public function testBucketsAreListed(): void
    {
        $transactions = [];

        $config = $this->getConfigWithMockedHandlers($transactions, [
            new Response(200, [], '["mybucket"]'),
        ]);

        $client = Client::make('http://gateway', 'acesskey', 'secretkey', $config);

        $response = $client->bucket()->list();

        $this->assertEquals(['mybucket'], $response->get());

        $this->assertCount(1, $transactions);

        /** @var Request $request */
        $request = $transactions[0]['request'];

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('gateway', $request->getUri()->getHost());
        $this->assertEquals('/admin/metadata/bucket', $request->getUri()->getPath());
    }

    /**
     * @covers \LBausch\PhpRadosgwAdmin\ApiRequest
     * @covers \LBausch\PhpRadosgwAdmin\ApiResponse
     * @covers \LBausch\PhpRadosgwAdmin\Client
     * @covers \LBausch\PhpRadosgwAdmin\Config
     * @covers \LBausch\PhpRadosgwAdmin\Middlewares\SignatureMiddleware
     * @covers \LBausch\PhpRadosgwAdmin\Resources\AbstractResource
     * @covers \LBausch\PhpRadosgwAdmin\Resources\Bucket::delete
     * @covers \LBausch\PhpRadosgwAdmin\Signature\SignatureV4::signRequest
     */
    public function testBucketIsDeleted(): void
    {
        $transactions = [];

        $config = $this->getConfigWithMockedHandlers($transactions, [
            new Response(200, [], '["mybucket"]'),
        ]);

        $client = Client::make('http://gateway', 'acesskey', 'secretkey', $config);

        $response = $client->bucket()->delete('mybucket');

        $this->assertEquals(['mybucket'], $response->get());

        $this->assertCount(1, $transactions);

        /** @var Request $request */
        $request = $transactions[0]['request'];

        $this->assertEquals('DELETE', $request->getMethod());
        $this->assertEquals('/admin/bucket', $request->getUri()->getPath());
        $this->assertEquals('bucket=mybucket', $request->getUri()->getQuery());
    }
}
