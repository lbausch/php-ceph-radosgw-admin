<?php

namespace Tests;

use LBausch\PhpRadosgwAdmin\Config;

final class ConfigTest extends TestCase
{
    /**
     * @covers \LBausch\PhpRadosgwAdmin\Config::__construct
     * @covers \LBausch\PhpRadosgwAdmin\Config::defaults
     * @covers \LBausch\PhpRadosgwAdmin\Config::get
     * @covers \LBausch\PhpRadosgwAdmin\Config::make
     * @covers \LBausch\PhpRadosgwAdmin\Config::set
     */
    public function testFactoryCreatesConfig(): void
    {
        $config = Config::make([
            'adminPath' => 'adm/',
        ]);

        $this->assertInstanceOf(Config::class, $config);
        $this->assertEquals('s3', $config->get('service'));
        $this->assertEquals('adm/', $config->get('adminPath'));

        $config->set('adminPath', 'administrator/');

        $this->assertEquals('administrator/', $config->get('adminPath'));
    }
}
