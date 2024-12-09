<?php

namespace Tests;

use Aws\S3\S3Client;
use GuzzleHttp\ClientInterface;
use InvalidArgumentException;
use LBausch\CephRadosgwAdmin\Client;
use PHPUnit\Framework\Attributes\CoversMethod;
use ReflectionObject;

#[CoversMethod(Client::class, 'make')]
#[CoversMethod(Client::class, '__call')]
#[CoversMethod(Client::class, '__construct')]
#[CoversMethod(Client::class, 'getHttpClient')]
#[CoversMethod(Client::class, 'getS3Client')]
#[CoversMethod(\LBausch\CephRadosgwAdmin\Middlewares\SignatureMiddleware::class, 'handle')]
#[CoversMethod(\LBausch\CephRadosgwAdmin\Config::class, '__construct')]
#[CoversMethod(\LBausch\CephRadosgwAdmin\Config::class, 'defaults')]
#[CoversMethod(\LBausch\CephRadosgwAdmin\Config::class, 'get')]
#[CoversMethod(\LBausch\CephRadosgwAdmin\Config::class, 'make')]
#[CoversMethod(\LBausch\CephRadosgwAdmin\Config::class, 'set')]
final class ClientTest extends TestCase
{
    public function testFactoryCreatesClients(): void
    {
        $client = Client::make('http://gateway:8080', 'key', 'secret');

        $this->assertInstanceOf(Client::class, $client);
        $this->assertInstanceOf(ClientInterface::class, $client->getHttpClient());
        $this->assertInstanceOf(S3Client::class, $client->getS3Client());
    }

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

    public function testInvalidResourceThrowsException(): void
    {
        $client = Client::make('http://gateway:8080', 'key', 'secret');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid resource LBausch\CephRadosgwAdmin\Resources\Foo');

        $client->foo(); // @phpstan-ignore-line
    }
}
