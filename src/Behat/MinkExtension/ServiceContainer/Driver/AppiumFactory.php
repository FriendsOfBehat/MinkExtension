<?php

namespace Behat\MinkExtension\ServiceContainer\Driver;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\Definition;

class AppiumFactory extends Selenium2Factory
{
    public function getDriverName(): string
    {
        return 'appium';
    }

    public function configure(ArrayNodeDefinition $builder): void
    {
        $builder
            ->children()
                  ->scalarNode('browser')->defaultValue('remote')->end()
                  ->append($this->getCapabilitiesNode())
                  ->scalarNode('appium_host')->defaultValue('0.0.0.0')->end()
                  ->scalarNode('appium_port')->defaultValue('4723')->end()
            ->end()
        ;
    }

    /**
     * @param array<string, mixed> $config
     */
    public function buildDriver(array $config): Definition
    {
        $host = (is_string($config['appium_host']) ? $config['appium_host'] : '').':'.(is_string($config['appium_port']) ? $config['appium_port'] : '');

        $config['wd_host'] = sprintf('%s/wd/hub', $host);

        return parent::buildDriver($config);
    }

    protected function getCapabilitiesNode(): ArrayNodeDefinition
    {
        $node = parent::getCapabilitiesNode();

        $node
            ->children()
                ->scalarNode('automationName')->defaultValue('Appium')->end()
                ->scalarNode('platformName')->end()
                ->scalarNode('platformVersion')->end()
                ->scalarNode('deviceName')->end()
                ->scalarNode('app')->end()
                ->scalarNode('browserName')->end()
                ->scalarNode('newCommandTimeout')->end()
                ->booleanNode('autoLaunch')->end()
                ->scalarNode('language')->end()
                ->scalarNode('locale')->end()
                ->scalarNode('udid')->end()
                ->scalarNode('orientation')->end()
                ->booleanNode('autoWebview')->end()
                ->booleanNode('noReset')->end()
                ->booleanNode('fullReset')->end()
            // ANDROID ONLY
                ->scalarNode('appActivity')->end()
                ->scalarNode('appPackage')->end()
                ->scalarNode('appWaitActivity')->end()
                ->scalarNode('appWaitPackage')->end()
                ->scalarNode('deviceReadyTimeout')->end()
                ->scalarNode('androidCoverage')->end()
                ->scalarNode('androidDeviceReadyTimeout')->end()
                ->scalarNode('androidDeviceSocket')->end()
                ->scalarNode('avd')->end()
                ->scalarNode('avdLaunchTimeout')->end()
                ->scalarNode('avdReadyTimeout')->end()
                ->scalarNode('avdArgs')->end()
                ->scalarNode('keystorePassword')->end()
                ->scalarNode('keystorePath')->end()
                ->scalarNode('keyAlias')->end()
                ->scalarNode('keyPassword')->end()
                ->scalarNode('chromedriverExecutable')->end()
                ->scalarNode('autoWebviewTimeout')->end()
                ->scalarNode('intentAction')->end()
                ->scalarNode('intentCategory')->end()
                ->scalarNode('intentFlags')->end()
                ->scalarNode('optionalIntentArguments')->end()
                ->booleanNode('enablePerformanceLogging')->end()
                ->booleanNode('useKeystore')->end()
                ->booleanNode('stopAppOnReset')->end()
                ->booleanNode('unicodeKeyboard')->end()
                ->booleanNode('resetKeyboard')->end()
                ->booleanNode('noSign')->end()
                ->booleanNode('ignoreUnimportantViews')->end()
           // IOS ONLY
                ->scalarNode('calendarFormat')->end()
                ->scalarNode('bundleId')->end()
                ->scalarNode('udid')->end()
                ->scalarNode('launchTimeout')->end()
                ->scalarNode('localizableStringsDir')->end()
                ->scalarNode('processArguments')->end()
                ->scalarNode('interKeyDelay')->end()
                ->scalarNode('sendKeyStrategy')->end()
                ->scalarNode('screenshotWaitTimeout')->end()
                ->scalarNode('waitForAppScript')->end()
                ->booleanNode('locationServicesEnabled')->end()
                ->booleanNode('locationServicesAuthorized')->end()
                ->booleanNode('autoAcceptAlerts')->end()
                ->booleanNode('autoDismissAlerts')->end()
                ->booleanNode('nativeInstrumentsLib')->end()
                ->booleanNode('nativeWebTap')->end()
                ->booleanNode('safariAllowPopups')->end()
                ->booleanNode('safariIgnoreFraudWarning')->end()
                ->booleanNode('safariOpenLinksInBackground')->end()
                ->booleanNode('keepKeyChains')->end()
                ->booleanNode('showIOSLog')->end()
            ->end()
        ;

        return $node;
    }
}
