<?php

declare(strict_types=1);

namespace Wtf\Core\Tests\Dummy;

class Provider implements \Pimple\ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(\Pimple\Container $container): void
    {
        $container['hello_world'] = function ($c) {
            return 'Hello, world!';
        };

        $container['example_middleware'] = $container->protect(function ($request, $response, $next) use ($container) {
            return $next($request, $response);
        });
    }
}
