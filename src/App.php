<?php

declare(strict_types=1);

namespace Wtf;

use League\Container\Container;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;
use Slim\Interfaces\RouteGroupInterface;
use Slim\Interfaces\RouteInterface;
use Slim\Middleware\ErrorMiddleware;
use Slim\Middleware\RoutingMiddleware;

class App
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var \Slim\App
     */
    protected $slim;

    /**
     * @param string $configPath Path to config directory
     */
    public function __construct(string $configPath)
    {
        $this->container = $this->initContainer($configPath);
        $this->slim = $this->initSlim();
    }

    /**
     * Init PSR-11 container with all WTF stuff included.
     *
     * @param string $configPath Path to config dir
     *
     * @return ContainerInterface
     */
    public function initContainer(string $configPath): ContainerInterface
    {
        $container = new Container();
        $container->defaultToShared(true);
        $container->add('__wtf_config_path', $configPath);
        $container->addServiceProvider('Wtf\Provider');
        // Load application service providers
        foreach ($container->get('config')('wtf.providers', []) as $provider) {
            $container->addServiceProvider($provider);
        }

        return $container;
    }

    /**
     * Init Slim framework app.
     *
     * @return \Slim\App
     */
    public function initSlim(): \Slim\App
    {
        $slim = AppFactory::create(null, $this->container);
        //Load application middlewares
        foreach ($this->container->get('config')('wtf.middlewares', []) as $middleware) {
            $slim->add($this->container->get($middleware));
        }
        $slim->add(new RoutingMiddleware($slim->getRouteResolver()));
        //@see https://github.com/slimphp/Slim/pull/2398
        $responseFactory = $slim->getResponseFactory();
        $errorMiddleware = new ErrorMiddleware($slim->getCallableResolver(), $responseFactory, true, true, true);
        $defaultErrorHandler = $this->container->get('config')('wtf.error.handlers.default', null);
        if ($defaultErrorHandler) {
            $errorMiddleware->setDefaultErrorHandler($this->container->get($defaultErrorHandler));
        }
        foreach ($this->container->get('config')('wtf.error.handlers.custom', []) as $exception => $handler) {
            $errorMiddleware->setErrorHandler($exception, $this->container->get($handler));
        }
        $slim->add($errorMiddleware);
        $this->container->get('__wtf_router')->run($slim);

        return $slim;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * @return ContainerInterface
     */
    public function getSlim(): \Slim\App
    {
        return $this->slim;
    }

    /**
     * Add GET route.
     *
     * @param string          $pattern  The route URI pattern
     * @param callable|string $callable The route callback routine
     *
     * @return RouteInterface
     */
    public function get(string $pattern, $callable): RouteInterface
    {
        return $this->slim->get($pattern, $callable);
    }

    /**
     * Add POST route.
     *
     * @param string          $pattern  The route URI pattern
     * @param callable|string $callable The route callback routine
     *
     * @return RouteInterface
     */
    public function post(string $pattern, $callable): RouteInterface
    {
        return $this->slim->post($pattern, $callable);
    }

    /**
     * Add PUT route.
     *
     * @param string          $pattern  The route URI pattern
     * @param callable|string $callable The route callback routine
     *
     * @return RouteInterface
     */
    public function put(string $pattern, $callable): RouteInterface
    {
        return $this->slim->put($pattern, $callable);
    }

    /**
     * Add PATCH route.
     *
     * @param string          $pattern  The route URI pattern
     * @param callable|string $callable The route callback routine
     *
     * @return RouteInterface
     */
    public function patch(string $pattern, $callable): RouteInterface
    {
        return $this->slim->patch($pattern, $callable);
    }

    /**
     * Add DELETE route.
     *
     * @param string          $pattern  The route URI pattern
     * @param callable|string $callable The route callback routine
     *
     * @return RouteInterface
     */
    public function delete(string $pattern, $callable): RouteInterface
    {
        return $this->slim->delete($pattern, $callable);
    }

    /**
     * Add OPTIONS route.
     *
     * @param string          $pattern  The route URI pattern
     * @param callable|string $callable The route callback routine
     *
     * @return RouteInterface
     */
    public function options(string $pattern, $callable): RouteInterface
    {
        return $this->slim->options($pattern, $callable);
    }

    /**
     * Add route for any HTTP method.
     *
     * @param string          $pattern  The route URI pattern
     * @param callable|string $callable The route callback routine
     *
     * @return RouteInterface
     */
    public function any(string $pattern, $callable): RouteInterface
    {
        return $this->slim->any($pattern, $callable);
    }

    /**
     * Add route with multiple methods.
     *
     * @param string[]        $methods  Numeric array of HTTP method names
     * @param string          $pattern  The route URI pattern
     * @param callable|string $callable The route callback routine
     *
     * @return RouteInterface
     */
    public function map(array $methods, string $pattern, $callable): RouteInterface
    {
        return $this->slim->map($methods, $pattern, $callable);
    }

    /**
     * Route Groups.
     *
     * This method accepts a route pattern and a callback. All route
     * declarations in the callback will be prepended by the group(s)
     * that it is in.
     *
     * @param string   $pattern
     * @param callable $callable
     *
     * @return RouteGroupInterface
     */
    public function group(string $pattern, $callable): RouteGroupInterface
    {
        return $this->slim->group($pattern, $callable);
    }

    /**
     * Add a route that sends an HTTP redirect.
     *
     * @param string              $from
     * @param string|UriInterface $to
     * @param int                 $status
     *
     * @return RouteInterface
     */
    public function redirect(string $from, $to, int $status = 302): RouteInterface
    {
        return $this->slim->redirect($from, $to, $status);
    }

    /**
     * Run application.
     *
     * This method traverses the application middleware stack and then sends the
     * resultant Response object to the HTTP client.
     *
     * @param null|ServerRequestInterface $request
     */
    public function run(ServerRequestInterface $request = null): void
    {
        $this->slim->run($request);
    }
}
