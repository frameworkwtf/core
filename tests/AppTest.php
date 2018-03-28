<?php

declare(strict_types=1);

namespace Wtf\Core\Tests;

use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
    public function testWithConfigDir(): void
    {
        $dir = __DIR__.'/data/config';
        $app = new \Wtf\App(['config_dir' => $dir]);
        $this->assertEquals($dir, $app->getContainer()->get('config_dir'));
    }

    public function testWithoutConfigDir(): void
    {
        $app = new \Wtf\App([]);
        $this->assertEquals(\getcwd().'/config', $app->getContainer()->get('config_dir'));
    }

    public function testCustomProviders(): void
    {
        $dir = __DIR__.'/data/config';
        $app = new \Wtf\App(['config_dir' => $dir]);
        $this->assertContains('\Wtf\Core\Tests\Dummy\Provider', $app->getContainer()['config']('suit.providers'));
    }

    public function testCustomMiddlewares(): void
    {
        $dir = __DIR__.'/data/config';
        $app = new \Wtf\App(['config_dir' => $dir]);
        $this->assertContains('example_middleware', $app->getContainer()['config']('suit.middlewares'));
    }

    public function testRun(): void
    {
        $dir = __DIR__.'/data/config';
        $app = new \Wtf\App(['config_dir' => $dir]);
        $response = $app->run(true);
        $this->assertInstanceOf('\Psr\Http\Message\ResponseInterface', $response);
        //test app_router
        foreach ($app->getContainer()->get('router')->getRoutes() as $route) {
            $this->assertAttributeContains('/test/route', 'pattern', $route);
        }

        $appRouter = $app->getContainer()->get('app_router');
        $appRouter->__invoke($app);
    }
}
