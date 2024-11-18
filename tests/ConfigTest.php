<?php

namespace Tests;

use LBausch\CephRadosgwAdmin\Config;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversMethod(Config::class, '__construct')]
#[CoversMethod(Config::class, 'defaults')]
#[CoversMethod(Config::class, 'get')]
#[CoversMethod(Config::class, 'make')]
#[CoversMethod(Config::class, 'set')]
final class ConfigTest extends TestCase
{
    public function testFactoryCreatesConfig(): void
    {
        $config = Config::make([
            'adminPath' => 'adm/',
        ]);

        $this->assertInstanceOf(Config::class, $config);
        $this->assertSame('s3', $config->get('service'));
        $this->assertSame('adm/', $config->get('adminPath'));

        $config->set('adminPath', 'administrator/');

        $this->assertSame('administrator/', $config->get('adminPath'));
    }
}
