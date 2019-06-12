<?php

declare(strict_types=1);

namespace Wtf;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

/**
 * Wtf Service Provider.
 */
class Provider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        'config',
        'Wtf\Config',
    ];

    /**
     * {@inheritdoc}
     */
    public function register(): void
    {
        $this->getContainer()->add('config', 'Wtf\Config')->addArgument('__wtf_config_path');
        //$container['app_router'] = function ($c) {
            //return new Router($c);
        //};
        //$container['globalrequest_middleware'] = $container->protect(function ($request, $response, $next) use ($container) {
            //if ($container->has('request')) {
                //unset($container['request']);
                //$container['request'] = $request;
            //}

            //return $next($request, $response);
        //});
        //$container['sentry'] = $this->getSentry($container);
        //$container['controller'] = $this->setControllerLoader($container);
        //$container['errorHandler'] = $this->setErrorHandler($container);
        //$container['phpErrorHandler'] = $this->setErrorHandler($container);
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
     * Set controller() function into container.
     *
     * @param Container $container
     *
     * @return callable
     */
    protected function setControllerLoader(Container $container): callable
    {
        return $container->protect(function (string $name) use ($container) {
            $parts = \explode('_', $name);
            $class = $container['config']('suit.namespaces.controller', '\\App\\Controller\\');
            foreach ($parts as $part) {
                $class .= \ucfirst($part);
            }
            if (!$container->has('controller_'.$class)) {
                $container['controller_'.$class] = function ($container) use ($class) {
                    return new $class($container);
                };
            }

            return $container['controller_'.$class];
        });
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
