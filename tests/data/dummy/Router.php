<?php

declare(strict_types=1);

namespace Wtf\Core\Tests\Dummy;

class Router extends \Wtf\Root
{
    public function __invoke(\Slim\App $app): void
    {
        foreach ($app->getContainer()['config']('routes') as $pattern => $info) {
            $app->map($info['methods'] ?? ['GET'], $pattern, $info['closure'] ?? function ($request, $response, $args) {
                return $response->write('Hello world!');
            });
        }
    }
}
