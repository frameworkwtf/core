<?php

declare(strict_types=1);

return [
    'providers' => [
        '\Wtf\Core\Tests\Dummy\Provider',
    ],
    'middlewares' => [
        'example_middleware',
    ],
    'namespace' => [
        'controller' => '\Wtf\Core\Tests\Dummy\\',
    ],
    'error' => [
        'handlers' => [
            'default' => 'defaultErrorHandler',
            'custom' => [
                '\RedisException' => 'redisErrorHandler',
            ],
        ],
        'sentry' => [
            'dsn' => 'https://fa38d114872b4533834f0ffd53e59ddc@sentry.io/211424', //demo project
        ],
    ],
    'dummy' => [
        'has' => 'something',
    ],
];
