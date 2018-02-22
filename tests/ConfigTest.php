<?php

declare(strict_types=1);

namespace Wtf\Core\Tests;

use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    protected $app;

    protected function setUp(): void
    {
        $dir = __DIR__.'/data/config';
        $this->app = new \Wtf\App(['config_dir' => $dir]);
    }

    public function testGetGroup(): void
    {
        $this->assertArrayHasKey('dummy', $this->app->getContainer()['config']('suit'));
        $this->assertEquals('something', $this->app->getContainer()['config']('suit.dummy.has'));
    }

    public function testGetNotExists(): void
    {
        $this->assertNull($this->app->getContainer()['config']('not.exists'));
        $this->assertArrayNotHasKey('has2', $this->app->getContainer()['config']('suit.dummy'));
        $this->assertEquals('default', $this->app->getContainer()['config']('suit.dummy.not.exists', 'default'));
        $this->assertNull($this->app->getContainer()['config']('suit.notexists'));
    }
}
