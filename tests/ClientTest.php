<?php

namespace Tests;

use Aws\S3\S3Client;
use GuzzleHttp\ClientInterface;
use LBausch\PhpRadosgwAdmin\Client;

final class ClientTest extends TestCase
{
    /**
     * @covers \LBausch\PhpRadosgwAdmin\Client::make
     * @covers \LBausch\PhpRadosgwAdmin\Client::__construct
     * @covers \LBausch\PhpRadosgwAdmin\Client::getHttpClient
     * @covers \LBausch\PhpRadosgwAdmin\Client::getS3Client
     * @covers \LBausch\PhpRadosgwAdmin\Middlewares\SignatureMiddleware::handle
     * @covers \LBausch\PhpRadosgwAdmin\Config::__construct
     * @covers \LBausch\PhpRadosgwAdmin\Config::defaults
     * @covers \LBausch\PhpRadosgwAdmin\Config::get
     * @covers \LBausch\PhpRadosgwAdmin\Config::make
     * @covers \LBausch\PhpRadosgwAdmin\Config::set
     */
    public function testFactoryCreatesClients(): void
    {
        $client = Client::make('http://gateway:8080', 'key', 'secret');

        $this->assertInstanceOf(Client::class, $client);
        $this->assertInstanceOf(ClientInterface::class, $client->getHttpClient());
        $this->assertInstanceOf(S3Client::class, $client->getS3Client());
    }
}
