<?php

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\CoversNothing;

#[CoversNothing]
final class BucketTest extends IntegrationTestCase
{
    private string $uid;

    private string $bucket;

    protected function setUp(): void
    {
        parent::setUp();

        $this->uid = $this->createUser();
        $this->bucket = $this->uniqueName('bucket');

        $s3client = $this->s3ClientFor($this->uid);

        $s3client->createBucket(['Bucket' => $this->bucket]);
        $s3client->putObject(['Bucket' => $this->bucket, 'Key' => 'object', 'Body' => 'content']);
    }

    public function testBucketsAreListed(): void
    {
        $this->assertContains($this->bucket, $this->client->bucket()->list()->get());
        $this->assertSame([$this->bucket], $this->client->bucket()->info(['uid' => $this->uid])->get());
    }

    public function testBucketInfoIsReturned(): void
    {
        $info = $this->client->bucket()->info(['bucket' => $this->bucket]);

        $this->assertSame($this->bucket, $info->get('bucket'));
        $this->assertSame($this->uid, $info->get('owner'));
    }

    public function testBucketIndexIsChecked(): void
    {
        $this->assertTrue($this->client->bucket()->check($this->bucket)->has('check_result'));

        $result = $this->client->bucket()->check($this->bucket, [
            'check-objects' => 'true',
            'fix' => 'true',
        ]);

        $this->assertTrue($result->has('check_result'));
    }

    public function testPoliciesAreRead(): void
    {
        $bucketPolicy = $this->client->bucket()->policy($this->bucket);

        $this->assertTrue($bucketPolicy->has('acl'));
        $this->assertTrue($bucketPolicy->has('owner'));

        $objectPolicy = $this->client->bucket()->policy($this->bucket, ['object' => 'object']);

        $this->assertTrue($objectPolicy->has('acl'));
    }

    public function testBucketIsUnlinkedAndLinked(): void
    {
        $this->client->bucket()->unlink($this->bucket, $this->uid);

        $this->assertSame([], $this->client->bucket()->info(['uid' => $this->uid])->get());

        $newOwner = $this->createUser();

        $this->client->bucket()->link($this->bucket, $newOwner);

        $this->assertSame($newOwner, $this->client->bucket()->info(['bucket' => $this->bucket])->get('owner'));
    }

    public function testBucketQuotaIsSet(): void
    {
        $this->client->bucket()->setQuota($this->uid, $this->bucket, [
            'enabled' => true,
            'max_size' => 4194304,
            'max_objects' => 3,
        ]);

        $quota = $this->client->bucket()->info(['bucket' => $this->bucket])->get('bucket_quota');

        $this->assertTrue($quota['enabled']);
        $this->assertSame(4194304, $quota['max_size']);
        $this->assertSame(3, $quota['max_objects']);
    }

    public function testObjectIsRemoved(): void
    {
        $this->client->bucket()->removeObject($this->bucket, 'object');

        $objects = $this->s3ClientFor($this->uid)->listObjectsV2(['Bucket' => $this->bucket]);

        $this->assertSame(0, $objects['KeyCount']);
    }

    public function testBucketIsRemoved(): void
    {
        $this->client->bucket()->remove($this->bucket, ['purge-objects' => 'true']);

        $this->assertNotContains($this->bucket, $this->client->bucket()->list()->get());
    }
}
