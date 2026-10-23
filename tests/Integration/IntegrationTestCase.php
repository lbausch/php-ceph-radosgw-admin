<?php

namespace Tests\Integration;

use Aws\S3\S3Client;
use LBausch\CephRadosgwAdmin\ApiException;
use LBausch\CephRadosgwAdmin\Client;
use PHPUnit\Framework\TestCase;

/**
 * Base class for tests running against a live Ceph Object Gateway.
 *
 * @see .github/ceph/start-rgw.sh
 */
abstract class IntegrationTestCase extends TestCase
{
    protected Client $client;

    /**
     * Users created during a test, purged afterwards.
     *
     * @var array<int, string>
     */
    private array $users = [];

    protected function setUp(): void
    {
        $endpoint = getenv('RGW_ENDPOINT');
        $accessKey = getenv('RGW_ACCESS_KEY');
        $secretKey = getenv('RGW_SECRET_KEY');

        if (false === $endpoint || false === $accessKey || false === $secretKey) {
            $this->markTestSkipped('Set RGW_ENDPOINT, RGW_ACCESS_KEY and RGW_SECRET_KEY to run integration tests.');
        }

        $this->client = Client::make($endpoint, $accessKey, $secretKey);
    }

    protected function tearDown(): void
    {
        foreach ($this->users as $uid) {
            try {
                $this->client->user()->remove($uid, true);
            } catch (ApiException) {
                // Already removed by the test
            }
        }
    }

    /**
     * Create a user which is purged after the test.
     */
    protected function createUser(): string
    {
        $uid = $this->uniqueName('user');

        $this->client->user()->create($uid, 'Integration Test User');

        $this->users[] = $uid;

        return $uid;
    }

    /**
     * Get an S3 client authenticated as the given user.
     */
    protected function s3ClientFor(string $uid): S3Client
    {
        /** @var array<int, array{access_key: string, secret_key: string}> $keys */
        $keys = $this->client->user()->info($uid)->get('keys');

        return $this->client->getS3Client($keys[0]['access_key'], $keys[0]['secret_key'], [
            'use_path_style_endpoint' => true,
        ]);
    }

    /**
     * Generate a name which does not collide with other tests.
     */
    protected function uniqueName(string $prefix): string
    {
        return $prefix.'-'.bin2hex(random_bytes(4));
    }
}
