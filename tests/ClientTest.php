<?php

namespace Tests;

use Aws\S3\S3Client;
use GuzzleHttp\ClientInterface;
use LBausch\CephRadosgwAdmin\Client;

final class ClientTest extends TestCase
{
    /**
     * @covers \LBausch\CephRadosgwAdmin\Client::make
     * @covers \LBausch\CephRadosgwAdmin\Client::__construct
     * @covers \LBausch\CephRadosgwAdmin\Client::getHttpClient
     * @covers \LBausch\CephRadosgwAdmin\Client::getS3Client
     * @covers \LBausch\CephRadosgwAdmin\Middlewares\SignatureMiddleware::handle
     * @covers \LBausch\CephRadosgwAdmin\Config::__construct
     * @covers \LBausch\CephRadosgwAdmin\Config::defaults
     * @covers \LBausch\CephRadosgwAdmin\Config::get
     * @covers \LBausch\CephRadosgwAdmin\Config::make
     * @covers \LBausch\CephRadosgwAdmin\Config::set
     */
    public function testFactoryCreatesClients(): void
    {
        $client = Client::make('http://gateway:8080', 'key', 'secret');

        $this->assertInstanceOf(Client::class, $client);
        $this->assertInstanceOf(ClientInterface::class, $client->getHttpClient());
        $this->assertInstanceOf(S3Client::class, $client->getS3Client());
    }
}
