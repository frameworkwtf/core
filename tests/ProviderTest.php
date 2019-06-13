<?php

declare(strict_types=1);

namespace Wtf\Core\Tests;

use PHPUnit\Framework\TestCase;

class ProviderTest extends TestCase
{
    protected $container;

    protected function setUp(): void
    {
        $dir = __DIR__.'/data/config';
        $app = new \Wtf\App($dir);
        $this->container = $app->getContainer();
    }

    public function testAppRouter(): void
    {
        $this->assertInstanceOf('\Wtf\Router', $this->container->get('__wtf_router'));
    }
}
