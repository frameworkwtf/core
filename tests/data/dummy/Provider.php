<?php

declare(strict_types=1);

namespace Wtf\Core\Tests\Dummy;

use League\Container\ServiceProvider\AbstractServiceProvider;

class Provider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        'hello_world',
        'example_middleware',
        'defaultErrorHandler',
        'redisErrorHandler',
    ];

    /**
     * {@inheritdoc}
     */
    public function register(): void
    {
        $this->getContainer()->add('hello_world', function () {
            return 'Hello, world!';
        });

        $this->getContainer()->add('example_middleware', function () {
            return function ($request, $handler) {
                return $handler->handle($request);
            };
        });

        $this->getContainer()->add('defaultErrorHandler', function (): void {
            function ($request, $exception, $displayErrorDetails, $logErrors, $logErrorDetails) {
                $payload = ['error' => $exception->getMessage()];

                $response = new Response();
                $response->getBody()->write($payload);

                return $response;
            };
        });

        $this->getContainer()->add('redisErrorHandler', function (): void {
            function ($request, $exception, $displayErrorDetails, $logErrors, $logErrorDetails) {
                $payload = ['error' => $exception->getMessage()];

                $response = new Response();
                $response->getBody()->write($payload);

                return $response;
            };
        });
    }
}
