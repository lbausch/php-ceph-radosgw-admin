<?php

namespace Tests\Integration;

use LBausch\CephRadosgwAdmin\ApiException;
use PHPUnit\Framework\Attributes\CoversNothing;

#[CoversNothing]
final class UserTest extends IntegrationTestCase
{
    public function testUserLifecycle(): void
    {
        $uid = $this->createUser();

        $info = $this->client->user()->info($uid);

        $this->assertSame($uid, $info->get('user_id'));
        $this->assertSame('Integration Test User', $info->get('display_name'));
        $this->assertContains($uid, $this->client->user()->list()->get());

        $modified = $this->client->user()->modify($uid, [
            'display-name' => 'Modified User',
            'max-buckets' => 5,
        ]);

        $this->assertSame('Modified User', $modified->get('display_name'));
        $this->assertSame(5, $modified->get('max_buckets'));

        $this->client->user()->remove($uid, true);

        $this->assertNotContains($uid, $this->client->user()->list()->get());
    }

    public function testUnknownUserThrowsException(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('NoSuchUser');

        $this->client->user()->info($this->uniqueName('missing'));
    }

    public function testKeysAreCreatedAndRemoved(): void
    {
        $uid = $this->createUser();
        $accessKey = strtoupper($this->uniqueName('key'));

        $keys = $this->client->user()->createKey($uid, [
            'access-key' => $accessKey,
            'secret-key' => 'secret',
        ])->get();

        $this->assertContains($accessKey, array_column($keys, 'access_key'));

        $this->client->user()->removeKey($accessKey);

        $keys = $this->client->user()->info($uid)->get('keys');

        $this->assertNotContains($accessKey, array_column($keys, 'access_key'));
    }

    public function testSubusersAreCreatedModifiedAndRemoved(): void
    {
        $uid = $this->createUser();
        $subuser = $uid.':swift';

        $subusers = $this->client->user()->createSubuser($uid, $subuser, ['access' => 'read'])->get();

        $this->assertSame([['id' => $subuser, 'permissions' => 'read']], $subusers);

        $subusers = $this->client->user()->modifySubuser($uid, $subuser, ['access' => 'full'])->get();

        $this->assertSame([['id' => $subuser, 'permissions' => 'full-control']], $subusers);

        $this->client->user()->removeSubuser($uid, $subuser);

        $this->assertSame([], $this->client->user()->info($uid)->get('subusers'));
    }

    public function testCapabilitiesAreAddedAndRemoved(): void
    {
        $uid = $this->createUser();

        $caps = $this->client->user()->addCapability($uid, 'usage=read')->get();

        $this->assertSame([['type' => 'usage', 'perm' => 'read']], $caps);

        $caps = $this->client->user()->removeCapability($uid, 'usage=read')->get();

        $this->assertSame([], $caps);
    }

    public function testUserQuotaIsSetAndRead(): void
    {
        $uid = $this->createUser();

        $this->client->user()->setQuota($uid, [
            'enabled' => true,
            'max_size' => 1048576,
            'max_objects' => 100,
        ]);

        $quota = $this->client->user()->getQuota($uid);

        $this->assertTrue($quota->get('enabled'));
        $this->assertSame(1048576, $quota->get('max_size'));
        $this->assertSame(100, $quota->get('max_objects'));
    }

    public function testBucketQuotaIsSetAndRead(): void
    {
        $uid = $this->createUser();

        $this->client->user()->setBucketQuota($uid, [
            'enabled' => true,
            'max_size' => 2097152,
            'max_objects' => 200,
        ]);

        $quota = $this->client->user()->getBucketQuota($uid);

        $this->assertTrue($quota->get('enabled'));
        $this->assertSame(2097152, $quota->get('max_size'));
        $this->assertSame(200, $quota->get('max_objects'));
    }
}
