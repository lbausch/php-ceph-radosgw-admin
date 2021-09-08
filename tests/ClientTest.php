<?php

namespace Tests;

use Aws\S3\S3Client;
use GuzzleHttp\ClientInterface;
use InvalidArgumentException;
use LBausch\CephRadosgwAdmin\Client;
use ReflectionObject;

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

    /**
     * @covers \LBausch\CephRadosgwAdmin\Config
     * @covers \LBausch\CephRadosgwAdmin\Middlewares\SignatureMiddleware::handle
     * @covers \LBausch\CephRadosgwAdmin\Client::make
     * @covers \LBausch\CephRadosgwAdmin\Client::__construct
     * @covers \LBausch\CephRadosgwAdmin\Client::getS3Client
     */
    public function testS3ClientUsesProvidedCredentials(): void
    {
        $client = Client::make('http://gateway:8080', 'key', 'secret');

        $s3client = $client->getS3Client('foo', 'bar');

        $credentials = $s3client->getCredentials();

        $reflection = new ReflectionObject($credentials);

        $value = $reflection->getProperty('value');
        $value->setAccessible(true);

        $this->assertSame('foo', $value->getValue($credentials)->getAccessKeyId());
        $this->assertSame('bar', $value->getValue($credentials)->getSecretKey());
    }

    /**
     * @covers \LBausch\CephRadosgwAdmin\Config
     * @covers \LBausch\CephRadosgwAdmin\Middlewares\SignatureMiddleware::handle
     * @covers \LBausch\CephRadosgwAdmin\Client::make
     * @covers \LBausch\CephRadosgwAdmin\Client::__construct
     * @covers \LBausch\CephRadosgwAdmin\Client::getS3Client
     */
    public function testS3ClientUsesProvidedOptions(): void
    {
        $client = Client::make('http://gateway:8080', 'key', 'secret');

        $s3client = $client->getS3Client('foo', 'bar', [
            'http' => [
                'verify' => false,
            ],
        ]);

        $command = $s3client->getCommand('ListBuckets');

        $data = $command->toArray();

        $this->assertArrayHasKey('@http', $data);
        $this->assertArrayHasKey('verify', $data['@http']);
        $this->assertFalse($data['@http']['verify']);
    }

    /**
     * @covers \LBausch\CephRadosgwAdmin\Client
     * @covers \LBausch\CephRadosgwAdmin\Middlewares\SignatureMiddleware::handle
     * @covers \LBausch\CephRadosgwAdmin\Config
     */
    public function testInvalidResourceThrowsException(): void
    {
        $client = Client::make('http://gateway:8080', 'key', 'secret');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid resource LBausch\CephRadosgwAdmin\Resources\Foo');

        $client->foo(); // @phpstan-ignore-line
    }
}
