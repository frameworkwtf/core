<?php

declare(strict_types=1);

namespace Wtf;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

/**
 * Wtf Service Provider.
 */
class Provider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $container): void
    {
        $container['suit_config'] = $this->getSuitConfig();
        $container['config'] = $this->getConfig($container);
        $container['sentry'] = $this->getSentry($container);
        $container['errorHandler'] = $this->setErrorHandler($container);
        $container['phpErrorHandler'] = $this->setErrorHandler($container);
        $container['globalrequest_middleware'] = $container->protect(function ($request, $response, $next) use ($container) {
            if ($container->has('request')) {
                unset($container['request']);
                $container['request'] = $request;
            }

            return $next($request, $response);
        });
    }

    /**
     * Prepare config object.
     *
     * @return callable
     */
    protected function getSuitConfig(): callable
    {
        return function ($c) {
            return new Config($c);
        };
    }

    /**
     * Add 'shortcut' to suit config __invoke().
     *
     * @param Container $container
     *
     * @return callable
     */
    protected function getConfig(Container $container): callable
    {
        return $container->protect(function (...$args) use ($container) {
            return \call_user_func_array($container['suit_config'], $args);
        });
    }

    /**
     * Add Sentry integration.
     */
    protected function getSentry(Container $container): callable
    {
        return function ($c) {
            $config = $c['config']('suit.sentry');

            $client = new \Raven_Client($config['dsn'], $config['options'] ?? []);
            $client->install();
            if ($c->has('user')) {
                $client->user_context((array) $c->get('user'));
            }

            return $client;
        };
    }

    /**
     * Set error handler with sentry.
     *
     * @param Container $container
     *
     * @return callable
     */
    protected function setErrorHandler(Container $container): callable
    {
        return function (Container $container) {
            return function (ServerRequestInterface $request, ResponseInterface $response, Throwable $e) use ($container) {
                $container->sentry->captureException($e);
                if ($container->has('appErrorHandler')) {
                    return $container['appErrorHandler']->error500($request, $response, $e);
                }

                return $response->withStatus(500);
            };
        };
    }
}
