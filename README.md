# WTF Framework core
[![Build Status](https://travis-ci.org/frameworkwtf/core.svg?branch=master)](https://travis-ci.org/frameworkwtf/core) [![Coverage Status](https://coveralls.io/repos/frameworkwtf/core/badge.svg?branch=master&service=github)](https://coveralls.io/github/frameworkwtf/core?branch=master)

# Table of Contents

<!-- vim-markdown-toc GFM -->

+ [What the ...?](#what-the-)
    * [Concept](#concept)
    * [Basic Requirements](#basic-requirements)
    * [Real requirements](#real-requirements)
    * [Conclusion](#conclusion)
+ [How to start?](#how-to-start)
    * [How to use magic?](#how-to-use-magic)
    * [Core package is not enough! I wanna more!](#core-package-is-not-enough-i-wanna-more)
+ [Per-class docs](#per-class-docs)
    * [App](#app)
        - [Config dir](#config-dir)
        - [Suit config file](#suit-config-file)
        - [Application - level router](#application---level-router)
            + [Define it in your provider](#define-it-in-your-provider)
            + [Create list of routes](#create-list-of-routes)
            + [Create router class](#create-router-class)
        - [Providers](#providers)
    * [Config](#config)
    * [Root](#root)

<!-- vim-markdown-toc -->

# What the ...?

## Concept

As web developer, you need some infrastructure code for your applications. For example: dependency manager, router, DI container and so on.

We need such tool, too. That's why we created wtf.

Main idea is simple: we already implemented lots of infrastructure logic (config resolver, "magic system parent" and so on), we just need to bundle it in separate package to use in any other projects without Ctr+C/Ctr+V

## Basic Requirements

1. We need a simple http request/response handler
2. We need good DI container
3. We are too lazy to implement it ourself
4. Symfony, Laravel and so on is too big for us and have lots of disadvantages.

Ok, **Slim Framework** is our (good) choise!

## Real requirements

1. Well, Slim is really good, but we need some tool for convenient config management. Result: `Config` class, adopted from [PHPixie 2.x](https://github.com/dracony/phpixie-core). Thank you, [@dracony](https://github.com/dracony) :)
2. Ok, but we wanna magic! We don't want to call `$app->getContainer()->get('something')` each time, it's too long. Result: `Root` class from [TiSuit](https://github.com/tisuit) (wtf framework "grandpa")
3. Hm... Ok, but I really need to extend parent `Root` class. Welcome, Pimple Providers.
4. And final step: comfortable DI configuration (slim's `dependencies.php` is too ugly and unusable). Welcome `Provider` class, thank you, [Pimple](https://pimple.symfony.com)

## Conclusion

We built very flexible solution, which allow you to create business logic of your app, without any worries about infrastructure code (_note:_ 100% test coverage).

One more thing: `wtf/core` package is just main component. You can use it without any other, just as you use slim framework itself, but with wtf you will have all these magic (very simple and predictable, in fact. It's not as Spring in Java :) things and easy to use classes in your app with full backward-compability with Slim framework itself.


# How to start?

Just use Slim "Hello world":

```php
<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';

$app = new \Wtf\App;
$app->get('/hello/{name}', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
    $response->getBody()->write("Hello, $name");

    return $response;
});
$app->run();
```

## How to use magic?

You need to pass path to your config folder in App constructor (like slim settings, man: https://www.slimframework.com/docs/objects/application.html#application-configuration), example:

```php

$config = [
    'config_dir' => __DIR__.'/config',
    'settings' => [
        'displayErrorDetails' => true,

        'logger' => [
            'name' => 'slim-app',
            'level' => Monolog\Logger::DEBUG,
            'path' => __DIR__ . '/../logs/app.log',
        ],
    ],
];
$app = new \Wtf\App($config);
```

And now, magic!

```php
$root = new \Wtf\Root($app->getContainer());
echo $root->config('app.site.name', 'production');
```

Place file `app.php` in `__DIR__.'/config'` folder (you passed it in App constructor, remember?):

```php
<?php return [
    'site' => [
        'name' => 'wtf Example',
        'url' => 'https://example.com',
        'parent' => 'https://slimframework.com',
    ],
];
```

And so on :)

More detailed docs coming soon.

## Core package is not enough! I wanna more!

Ok, no problem! Just open [wtf repo list](https://github.com/wtf) and choose any other packages.

By the way, wtf architecture is like a lego bricks. You just got main "platform" with core package, need more functions? Add other packages in your `composer.json` and be happy :)

# Per-class docs

## App

It's just wrapper around `\Slim\App` to add some magic and autoloading

### Config dir

As we have `Config` class in `wtf/core`, we need to tell it, where to get configs. Thats why when you create app instance (`$app = new \Wtf\App($config)`)
you may pass `config_dir` as key of `$config` array. If fact, you need to pass only that key :)
Because on app construct stage, Config class will try to load suit config file with all configuration for Slim framework and wtf.

If you will not pass it, default used value will be `getcwd().'/config'`

### Suit config file

`suit.php` is start point. Here you can define list of autoloaded middlewares, list of required providers, slim framework settings and so on. Let's see example:

```php
<?php

declare(strict_types=1);

return [
    'providers' => [
        '\App\Provider', // You can define list of required service providers here. @see https://pimple.symfony.com/#extending-a-container
    ],
    'middlewares' => [
        'example_middleware',  // You can define list of autoloaded middlewares here. NOTE: each middleware MUST be defined in any loaded Provider class.
    ],
    'sentry' => [ // We have deep integration with sentry.io (and self-hosted, too) @see https://docs.sentry.io
        'dsn' => 'https://fa38d114872b4533834f0ffd53e59ddc:54ffe4da5b23455da1b93d4b6abc246e@sentry.io/211424', //demo project
        'options' => [],
    ],
    'settings' => [ // Slim framework settings @see https://www.slimframework.com/docs/objects/application.html#application-configuration
        'displayErrorDetails' => true,
        'determineRouteBeforeAppMiddleware' => true,
        'debug' => true,
    ],
];
```

### Application - level router

If you don't want to define routes in `index.php` file with closures (Slim framework choise :(), you can create app-level router class, which will handle routes, let's create one (for example).

#### Define it in your provider

You MUST use key `app_router` to define that class, something like this:

```php
$container['app_router'] = function ($c) {
    return new \YourApp\Router($c);
};
```

#### Create list of routes

> **NOTE**: It's just example, you can do all that you want

eg, `routes.php` in config dir:

```php
<?php
return [
    //Not good idea, MVC is the best :)
    '/' => function ($request, $response, $args) {
            return $response->write('Hello world!');
    },
];
```

#### Create router class

```php
<?php
namespace YourApp;

class Router extends \Wtf\Root
{
    // This class will be called via __invoke method
    public function __invoke(\Slim\App $app)
    {
        foreach($this->config('routes') as $pattern => $closure) {
            $app->any($pattern, $closure);
        }
    }
}
```

That's all :)
Try to open `/` in browser and see `Hello world!` :)

### Providers

Each package of `wtf` will give it's own provider for you. It's something like Symfony bundlers, but simple and less complicated :)

Provider documentation call be found here: https://pimple.symfony.com/#extending-a-container

You can add lot's of providers in `suit.php` config file (`providers` array), just add new line with classname of required provider and it will be loaded automatically

PS: Core has it's own provider, but it's loading before app providers

## Config

As it was told earlier, `Config` is adopted class of `PHPixie 2.x`, main idea is to give you easy way for work with configuration.

`Config` MUST be called via `__invoke()` method (like `$config('suit.settings')`), but feel free to use any other public method, they all well-documented (I hope..)

Inside your application you will call it from `Root` child, mostly: `$this->config('suit.settings')`

Config string MUST be separated by dots. First word (before first dot) is file name, all other - keys inside that file.

> **NOTE**: Config MAY return not only value, but group, too. If you call config and want to receive whole array of file or any of nested sets, just pass something like `suit` (to receive content of whole file) or `suit.settings` (to receive array of slim settings)

## Root

Magic. That's all :)

That class allow you to call functions from container, use magic getters/setters and so on. If you want to extend root app class, you MUST use that class as parent
