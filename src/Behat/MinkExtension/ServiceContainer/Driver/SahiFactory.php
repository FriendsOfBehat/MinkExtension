<?php

/*
 * This file is part of the Behat MinkExtension.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\MinkExtension\ServiceContainer\Driver;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\Definition;

class SahiFactory implements DriverFactory
{
    public function getDriverName(): string
    {
        return 'sahi';
    }

    public function supportsJavascript(): bool
    {
        return true;
    }

    public function configure(ArrayNodeDefinition $builder): void
    {
        $builder
            ->children()
                ->scalarNode('sid')->defaultNull()->end()
                ->scalarNode('host')->defaultValue('localhost')->end()
                ->scalarNode('port')->defaultValue(9999)->end()
                ->scalarNode('browser')->defaultNull()->end()
                ->scalarNode('limit')->defaultValue(600)->end()
            ->end()
        ;
    }

    /**
     * @param array<string, mixed> $config
     */
    public function buildDriver(array $config): Definition
    {
        trigger_deprecation('friends-of-behat/mink-extension', '2.8.0', 'Configuration for the "sahi" driver is deprecated, since the client implementation has been abandoned. Support for it will be removed in the next major version of this extension.');

        if (!class_exists('Behat\Mink\Driver\SahiDriver')) {
            throw new \RuntimeException('Install MinkSahiDriver in order to use sahi driver.');
        }

        return new Definition('Behat\Mink\Driver\SahiDriver', [
            '%mink.browser_name%',
            new Definition('Behat\SahiClient\Client', [
                new Definition('Behat\SahiClient\Connection', [
                    $config['sid'],
                    $config['host'],
                    $config['port'],
                    $config['browser'],
                    $config['limit'],
                ]),
            ]),
        ]);
    }
}
