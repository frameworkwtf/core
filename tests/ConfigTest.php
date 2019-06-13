<?php

declare(strict_types=1);

namespace Wtf\Core\Tests;

use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    protected $config;

    protected function setUp(): void
    {
        $dir = __DIR__.'/data/config';
        $app = new \Wtf\App($dir);
        $this->config = $app->getContainer()->get('config');
    }

    public function testGetGroup(): void
    {
        $this->assertArrayHasKey('dummy', $this->config->__invoke('wtf'));
        $this->assertEquals('something', $this->config->__invoke('wtf.dummy.has'));
    }

    public function testGetNotExists(): void
    {
        $this->assertNull($this->config->__invoke('not.exists'));
        $this->assertArrayNotHasKey('has2', $this->config->__invoke('wtf.dummy'));
        $this->assertEquals('default', $this->config->__invoke('wtf.dummy.not.exists', 'default'));
        $this->assertNull($this->config->__invoke('wtf.notexists'));
    }
}
