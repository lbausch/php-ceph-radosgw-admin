<?php

namespace Tests;

use LBausch\CephRadosgwAdmin\Config;

final class ConfigTest extends TestCase
{
    /**
     * @covers \LBausch\CephRadosgwAdmin\Config::__construct
     * @covers \LBausch\CephRadosgwAdmin\Config::defaults
     * @covers \LBausch\CephRadosgwAdmin\Config::get
     * @covers \LBausch\CephRadosgwAdmin\Config::make
     * @covers \LBausch\CephRadosgwAdmin\Config::set
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
