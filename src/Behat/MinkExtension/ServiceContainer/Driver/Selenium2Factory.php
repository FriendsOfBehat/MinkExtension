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

class Selenium2Factory implements DriverFactory
{
    use EnvironmentCapabilities;

    public function getDriverName(): string
    {
        return 'selenium2';
    }

    public function supportsJavascript(): bool
    {
        return true;
    }

    public function configure(ArrayNodeDefinition $builder): void
    {
        $builder
            ->children()
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
        if (!class_exists('Behat\Mink\Driver\Selenium2Driver')) {
            throw new \RuntimeException(sprintf('Install MinkSelenium2Driver in order to use %s driver.', $this->getDriverName()));
        }

        $capabilities = is_array($config['capabilities']) ? $config['capabilities'] : [];
        $extraCapabilities = is_array($capabilities['extra_capabilities']) ? $capabilities['extra_capabilities'] : [];
        unset($capabilities['extra_capabilities']);

        return new Definition('Behat\Mink\Driver\Selenium2Driver', [
            $config['browser'],
            array_replace($this->guessEnvironmentCapabilities(), $extraCapabilities, $capabilities),
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
                ->scalarNode('browserName')->end()
                ->scalarNode('version')->end()
                ->scalarNode('platform')->end()
                ->scalarNode('browserVersion')->end()
                ->scalarNode('browser')->defaultValue('firefox')->end()
                ->booleanNode('marionette')->end()
                ->booleanNode('ignoreZoomSetting')->defaultFalse()->end()
                ->scalarNode('name')->defaultValue('Behat feature suite')->end()
                ->scalarNode('deviceOrientation')->end()
                ->scalarNode('deviceType')->end()
                ->booleanNode('javascriptEnabled')->end()
                ->booleanNode('databaseEnabled')->end()
                ->booleanNode('locationContextEnabled')->end()
                ->booleanNode('applicationCacheEnabled')->end()
                ->booleanNode('browserConnectionEnabled')->end()
                ->booleanNode('webStorageEnabled')->end()
                ->booleanNode('rotatable')->end()
                ->booleanNode('acceptSslCerts')->end()
                ->booleanNode('nativeEvents')->end()
                ->booleanNode('overlappingCheckDisabled')->end()
                ->arrayNode('proxy')
                    ->children()
                        ->scalarNode('proxyType')->end()
                        ->scalarNode('proxyAuthconfigUrl')->end()
                        ->scalarNode('ftpProxy')->end()
                        ->scalarNode('httpProxy')->end()
                        ->scalarNode('sslProxy')->end()
                    ->end()
                    ->validate()
                        ->ifTrue(function (mixed $v): bool {
                            return empty($v);
                        })
                        ->thenUnset()
                    ->end()
                ->end()
                ->arrayNode('firefox')
                    ->children()
                        ->scalarNode('profile')
                            ->validate()
                                ->ifTrue(function (mixed $v): bool {
                                    return !is_string($v) || !file_exists($v);
                                })
                                ->thenInvalid('Cannot find profile zip file %s')
                            ->end()
                        ->end()
                        ->scalarNode('binary')->end()
                    ->end()
                ->end()
                ->arrayNode('chrome')
                    ->children()
                        ->arrayNode('switches')->prototype('scalar')->end()->end()
                        ->scalarNode('binary')->end()
                        ->arrayNode('extensions')->prototype('scalar')->end()->end()
                        ->arrayNode('prefs')
                            ->normalizeKeys(false)
                            ->useAttributeAsKey('name')
                            ->prototype('variable')->end()
                        ->end()
                    ->end()
                    ->validate()
                        ->ifTrue(function (mixed $v): bool {
                            return !is_array($v) || empty($v['prefs']);
                        })
                        ->then(function (mixed $v): mixed {
                            if (is_array($v)) {
                                unset($v['prefs']);
                            }

                            return $v;
                        })
                    ->end()
                ->end()
                ->arrayNode('extra_capabilities')
                    ->info('Custom capabilities merged with the known ones')
                    ->normalizeKeys(false)
                    ->useAttributeAsKey('name')
                    ->prototype('variable')->end()
                ->end()
            ->end();

        return $node;
    }
}
