<?php

declare(strict_types=1);

namespace Wtf;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Application level router.
 */
class Router extends \Wtf\Root
{
    public function getController(string $name): \Wtf\Root
    {
        $parts = \explode('_', $name);
        $class = $this->config('wtf.namespace.controller', '\\App\\Controller\\');
        foreach ($parts as $part) {
            $class .= \ucfirst($part);
        }

        return new $class($this->container);
    }

    /**
     * Map routes from `routes.php` config.
     *
     * @param \Slim\App $app
     */
    public function run(\Slim\App $app): void
    {
        foreach ($this->config('routes') as $group_name => $routes) {
            $app->group($group_name, function ($group) use ($group_name, $routes): void {
                $controller = ('/' === $group_name || !$group_name) ? 'index' : \trim($group_name, '/');

                foreach ($routes as $name => $route) {
                    $methods = $route['methods'] ?? ['GET'];
                    $pattern = $route['pattern'] ?? '';
                    $callable = function (Request $request, Response $response, array $args = []) use ($controller, $name) {
                        $args['action'] = $name;

                        return $this->get('__wtf_router')->getController($controller)->__invoke($request, $response, $args);
                    };
                    $group->map($methods, $pattern, $callable)->setName(('index' === $controller ? '' : $controller).'-'.$name);
                }
            });
        }
    }
}
