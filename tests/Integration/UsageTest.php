<?php

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\CoversNothing;

#[CoversNothing]
final class UsageTest extends IntegrationTestCase
{
    public function testUsageIsReturned(): void
    {
        $uid = $this->createUser();

        $usage = $this->client->usage()->info(['uid' => $uid]);

        $this->assertTrue($usage->has('entries'));
        $this->assertTrue($usage->has('summary'));
    }

    public function testUsageIsTrimmed(): void
    {
        $uid = $this->createUser();

        $response = $this->client->usage()->trim(['uid' => $uid]);

        $this->assertTrue($response->succeeded());
    }
}
