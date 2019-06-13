<?php

declare(strict_types=1);

namespace Wtf\Core\Tests;

use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
    public function testInit(): void
    {
        $dir = __DIR__.'/data/config';
        $app = new \Wtf\App($dir);
        $this->assertEquals($dir, $app->getContainer()->get('__wtf_config_path'));
    }

    public function testCustomProviders(): void
    {
        $dir = __DIR__.'/data/config';
        $app = new \Wtf\App($dir);
        $this->assertContains('\Wtf\Core\Tests\Dummy\Provider', $app->getContainer()->get('config')('wtf.providers', []));
    }

    public function testCustomMiddlewares(): void
    {
        $dir = __DIR__.'/data/config';
        $app = new \Wtf\App($dir);
        $this->assertContains('example_middleware', $app->getContainer()->get('config')('wtf.middlewares', []));
    }

    public function testProxiedMethods(): void
    {
        $dir = __DIR__.'/data/config';
        $app = new \Wtf\App($dir);
        $this->assertInstanceOf('Slim\Interfaces\RouteGroupInterface', $app->group('/', function ($group) { return $group; }));
        $this->assertInstanceOf('Slim\Interfaces\RouteInterface', $app->any('/', function ($request, $response) { return $response; }));
        $this->assertInstanceOf('Slim\Interfaces\RouteInterface', $app->delete('/', function ($request, $response) { return $response; }));
        $this->assertInstanceOf('Slim\Interfaces\RouteInterface', $app->get('/', function ($request, $response) { return $response; }));
        $this->assertInstanceOf('Slim\Interfaces\RouteInterface', $app->map(['GET'], '/', function ($request, $response) { return $response; }));
        $this->assertInstanceOf('Slim\Interfaces\RouteInterface', $app->options('/', function ($request, $response) { return $response; }));
        $this->assertInstanceOf('Slim\Interfaces\RouteInterface', $app->patch('/', function ($request, $response) { return $response; }));
        $this->assertInstanceOf('Slim\Interfaces\RouteInterface', $app->post('/', function ($request, $response) { return $response; }));
        $this->assertInstanceOf('Slim\Interfaces\RouteInterface', $app->put('/', function ($request, $response) { return $response; }));
        $this->assertInstanceOf('Slim\Interfaces\RouteInterface', $app->redirect('/', '/redirected'));
    }

    public function testRun(): void
    {
        $dir = __DIR__.'/data/config';
        $app = new \Wtf\App($dir);
        $app->get('/test', function ($request, $response) {
            $response->getBody()->write('Hello World');

            return $response;
        });
        $appRouter = $app->getContainer()->get('__wtf_router');
        $app->run();
        $this->expectOutputString('Hello World');
    }

    public function testAppRouter(): void
    {
        $dir = __DIR__.'/data/config';
        $app = new \Wtf\App($dir);
        $appRouter = $app->getContainer()->get('__wtf_router');
        $appRouter->run($app->getSlim());
        $this->expectOutputString('');
    }
}
