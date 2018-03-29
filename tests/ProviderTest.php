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
        $app = new \Wtf\App(['config_dir' => $dir]);
        $this->container = $app->getContainer();
    }

    public function testSentryWithoutUserContext(): void
    {
        $this->assertInstanceOf('\Raven_Client', $this->container->sentry);
        $this->assertNull($this->container->sentry->context->user);
    }

    public function testSentryWithUserContext(): void
    {
        $this->container['user'] = ['test' => 'user'];
        $this->assertInstanceOf('\Raven_Client', $this->container->sentry);
        $this->assertEquals(['test' => 'user'], $this->container->sentry->context->user);
    }

    public function testControllerLoader(): void
    {
        $controller = $this->container['controller']('dummy_controller');
        $this->assertInstanceOf('\Wtf\Root', $controller);
    }

    public function testErrorHander(): void
    {
        $middleware = $this->container->errorHandler;
        $this->assertInstanceOf('\Psr\Http\Message\ResponseInterface', $middleware($this->container->request, $this->container->response, new \Exception()));
    }

    public function testAppErrorHandler(): void
    {
        $this->container['appErrorHandler'] = function ($c) {
            return new Dummy\ErrorHandler($c);
        };

        $middleware = $this->container->errorHandler;
        $this->assertInstanceOf('\Psr\Http\Message\ResponseInterface', $middleware($this->container->request, $this->container->response, new \Exception()));
    }

    public function testPhpErrorHander(): void
    {
        $middleware = $this->container->phpErrorHandler;
        $this->assertInstanceOf('\Psr\Http\Message\ResponseInterface', $middleware($this->container->request, $this->container->response, new \Exception()));
    }
}
