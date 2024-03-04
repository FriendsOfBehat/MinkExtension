<?php

namespace Behat\MinkExtension\ServiceContainer\Driver;

use Mink\WebdriverClassicDriver\WebdriverClassicDriver;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\Definition;

class WebdriverClassicFactory implements DriverFactory
{
    use EnvironmentCapabilities;

    /**
     * {@inheritdoc}
     */
    public function getDriverName(): string
    {
        return 'webdriver-classic';
    }

    /**
     * {@inheritdoc}
     */
    public function supportsJavascript(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder): void
    {
        $builder
            ->children()
                ->scalarNode('browser')->defaultValue('%mink.browser_name%')->end()
                ->scalarNode('wd_host')->defaultValue('http://localhost:4444/wd/hub')->end()
                ->arrayNode('capabilities')
                    ->normalizeKeys(false)
                    ->useAttributeAsKey('name')
                    ->prototype('variable')->end()
                ->end()
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function buildDriver(array $config): Definition
    {
        if (!class_exists(WebdriverClassicDriver::class)) {
            throw new \RuntimeException(
                "Install mink/webdriver-classic-driver in order to use the {$this->getDriverName()} driver."
            );
        }

        return new Definition(WebdriverClassicDriver::class, [
            $config['browser'],
            array_merge(
                ['capabilities' => $config['capabilities']],
                $this->guessEnvironmentCapabilities()
            ),
            $config['wd_host'],
        ]);
    }
}
