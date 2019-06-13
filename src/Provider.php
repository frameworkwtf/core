<?php

declare(strict_types=1);

namespace Wtf;

use League\Container\ServiceProvider\AbstractServiceProvider;

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
        '__wtf_router',
    ];

    /**
     * {@inheritdoc}
     */
    public function register(): void
    {
        $this->getContainer()->add('config', 'Wtf\Config')->addArgument('__wtf_config_path');
        $this->getContainer()->add('__wtf_router', 'Wtf\Router')->addArgument($this->getContainer());
        //Init sentry. It does NOT create object, so we will not add it to container, just init that shit. Fucking magic.
        \Sentry\init($this->getContainer()->get('config')('wtf.error.sentry', []));
    }
}
