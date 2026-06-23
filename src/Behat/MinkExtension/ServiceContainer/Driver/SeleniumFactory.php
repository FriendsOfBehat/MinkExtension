<?php

/*
 * This file is part of the Behat MinkExtension.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\MinkExtension\ServiceContainer\Driver;

use Behat\Testwork\Deprecation\DeprecationCollector;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\Definition;

class SeleniumFactory implements DriverFactory
{
    public function getDriverName(): string
    {
        return 'selenium';
    }

    public function supportsJavascript(): bool
    {
        return true;
    }

    public function configure(ArrayNodeDefinition $builder): void
    {
        $builder
            ->children()
                ->scalarNode('host')->defaultValue('127.0.0.1')->end()
                ->scalarNode('port')->defaultValue(4444)->end()
                ->scalarNode('browser')->defaultValue('*%mink.browser_name%')->end()
            ->end()
        ;
    }

    /**
     * @param array<mixed> $config
     */
    public function buildDriver(array $config): Definition
    {
        DeprecationCollector::trigger('Since friends-of-behat/mink-extension 2.8.0: Configuration for the "selenium" driver is deprecated, since the client implementation has been abandoned. Support for it will be removed in the next major version of MinkExtension.');

        if (!class_exists('Behat\Mink\Driver\SeleniumDriver')) {
            throw new \RuntimeException('Install MinkSeleniumDriver in order to activate selenium session.');
        }

        return new Definition('Behat\Mink\Driver\SeleniumDriver', [
            $config['browser'],
            '%mink.base_url%',
            new Definition('Selenium\Client', [
                $config['host'],
                $config['port'],
            ]),
        ]);
    }
}
