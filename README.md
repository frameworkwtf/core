# WTF Framework core
[![Build Status](https://travis-ci.org/frameworkwtf/core.svg?branch=2.x)](https://travis-ci.org/frameworkwtf/core) [![Coverage Status](https://coveralls.io/repos/frameworkwtf/core/badge.svg?branch=2.x&service=github)](https://coveralls.io/github/frameworkwtf/core?branch=2.x)

[Documentation](https://framework.wtf)

## Changes in 2.x public api

### App initialization

**1.x**:

```php
<?php
$app = new \Wtf\App(['config_dir' => '/path/to/config/dir']);
```

**2.x**:

```php
<?php
$app = new \Wtf\App('/path/to/config/dir');
```

### Main config file

**1.x**: _for backward-compability with TiSuit_

```php
// config/suit.php
return [
    'providers' => [
        '\Wtf\Core\Tests\Dummy\Provider',
    ],
    'middlewares' => [
        'example_middleware',
    ],
    'sentry' => [ // all options will be used to init sentry client
        'dsn' => 'https://fa38d114872b4533834f0ffd53e59ddc:54ffe4da5b23455da1b93d4b6abc246e@sentry.io/211424', //demo project
    ],
    'namespaces' => [
        'controller' => '\Wtf\Core\Tests\Dummy\\',
    ],
];
```

**2.x**: _note file name_

```php
// config/wtf.php
return [
    'providers' => [
        '\Wtf\Core\Tests\Dummy\Provider',
    ],
    'middlewares' => [
        'example_middleware',
    ],
    'namespace' => [ //was: "namespaces"
        'controller' => '\Wtf\Core\Tests\Dummy\\',
    ],
    'error' => [ //was implemented via appErrorHandler.
        'handlers' => [
            'default' => 'defaultErrorHandler', //defaultErrorHandler is alias of object in container
            'custom' => [
                '\RedisException' => 'redisErrorHandler', //redisErrorHandler is alias of object in container
            ],
        ],
        'sentry' => [
            'dsn' => 'your DSN',
            'options' => [],
        ],
    ],
];
```
