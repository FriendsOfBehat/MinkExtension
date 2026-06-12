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

class Selenium4Factory implements DriverFactory
{
    use EnvironmentCapabilities;

    public function getDriverName(): string
    {
        return 'selenium4';
    }

    public function supportsJavascript(): bool
    {
        return true;
    }

    public function configure(ArrayNodeDefinition $builder): void
    {
        $builder
            ->children()
                ->scalarNode('name')->defaultValue('Behat Test')->end()
                ->scalarNode('browser')->defaultValue('%mink.browser_name%')->end()
                ->append($this->getCapabilitiesNode())
                ->scalarNode('wd_host')->defaultValue('http://localhost:4444/wd/hub')->end()
            ->end()
        ;
    }

    /**
     * @param array<mixed> $config
     */
    public function buildDriver(array $config): Definition
    {
        if (!class_exists('Behat\Mink\Driver\Selenium4Driver')) {
            throw new \RuntimeException(sprintf('Install MinkSelenium4Driver in order to use %s driver.', $this->getDriverName()));
        }

        return new Definition('Behat\Mink\Driver\Selenium4Driver', [
            $config['browser'],
            array_merge(
                $this->guessEnvironmentCapabilities(),
                is_array($config['capabilities']) ? $config['capabilities'] : []
            ),
            $config['wd_host'],
        ]);
    }

    protected function getCapabilitiesNode(): ArrayNodeDefinition
    {
        $node = new ArrayNodeDefinition('capabilities');

        $node
            ->addDefaultsIfNotSet()
            ->normalizeKeys(false)
            ->children()
                ->arrayNode('firstMatch')
                ->end()
                ->arrayNode('alwaysMatch')
                    ->children()
                        ->scalarNode('browserName')->end()
                        ->scalarNode('pageLoadStrategy')->end()
                        ->arrayNode('goog:chromeOptions')
                            ->children()
                                ->arrayNode('extensions')
                                    ->scalarPrototype()->end()
                                ->end()
                                ->arrayNode('args')
                                    ->scalarPrototype()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }
}
