<?php

/*
 * This file is part of the Behat MinkExtension.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\MinkExtension\ServiceContainer;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\MinkExtension\ServiceContainer\Driver\AppiumFactory;
use Behat\MinkExtension\ServiceContainer\Driver\BrowserKitFactory;
use Behat\MinkExtension\ServiceContainer\Driver\BrowserStackFactory;
use Behat\MinkExtension\ServiceContainer\Driver\DriverFactory;
use Behat\MinkExtension\ServiceContainer\Driver\SahiFactory;
use Behat\MinkExtension\ServiceContainer\Driver\SauceLabsFactory;
use Behat\MinkExtension\ServiceContainer\Driver\Selenium2Factory;
use Behat\MinkExtension\ServiceContainer\Driver\Selenium4Factory;
use Behat\MinkExtension\ServiceContainer\Driver\SeleniumFactory;
use Behat\MinkExtension\ServiceContainer\Driver\WebdriverClassicFactory;
use Behat\MinkExtension\ServiceContainer\Driver\ZombieFactory;
use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\ServiceContainer\Exception\ProcessingException;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Mink extension for Behat class.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 *
 * @final since 2.8.0
 */
class MinkExtension implements ExtensionInterface
{
    public const MINK_ID = 'mink';
    public const SELECTORS_HANDLER_ID = 'mink.selectors_handler';

    public const SELECTOR_TAG = 'mink.selector';

    /**
     * @var DriverFactory[]
     */
    private $driverFactories = [];

    public function __construct()
    {
        $this->registerDriverFactory(new BrowserKitFactory());
        $this->registerDriverFactory(new SahiFactory());
        $this->registerDriverFactory(new SeleniumFactory());
        $this->registerDriverFactory(new Selenium2Factory());
        $this->registerDriverFactory(new Selenium4Factory());
        $this->registerDriverFactory(new SauceLabsFactory());
        $this->registerDriverFactory(new BrowserStackFactory());
        $this->registerDriverFactory(new ZombieFactory());
        $this->registerDriverFactory(new AppiumFactory());
        $this->registerDriverFactory(new WebdriverClassicFactory());
    }

    public function registerDriverFactory(DriverFactory $driverFactory): void
    {
        $this->driverFactories[$driverFactory->getDriverName()] = $driverFactory;
    }

    public function load(ContainerBuilder $container, array $config): void
    {
        if (isset($config['mink_loader'])) {
            $basePath = $container->getParameter('paths.base');
            $basePath = is_string($basePath) ? $basePath : '';
            $minkLoader = is_string($config['mink_loader']) ? $config['mink_loader'] : '';

            if (file_exists($basePath.DIRECTORY_SEPARATOR.$minkLoader)) {
                require $basePath.DIRECTORY_SEPARATOR.$minkLoader;
            } else {
                require $minkLoader;
            }
        }

        $this->loadMink($container);
        $this->loadContextInitializer($container);
        $this->loadSelectorsHandler($container);
        $this->loadSessions($container, $config);
        $this->loadSessionsListener($container);

        if ($config['show_auto']) {
            $this->loadFailureShowListener($container);
        }

        unset($config['sessions']);

        $container->setParameter('mink.parameters', $config);
        $container->setParameter('mink.base_url', is_string($config['base_url'] ?? '') ? ($config['base_url'] ?? '') : '');
        $container->setParameter('mink.browser_name', is_string($config['browser_name'] ?? '') ? ($config['browser_name'] ?? '') : '');
    }

    public function configure(ArrayNodeDefinition $builder): void
    {
        // Rewrite keys to define a shortcut way without allowing conflicts with real keys
        $renamedKeys = array_diff(
            array_keys($this->driverFactories),
            ['mink_loader', 'base_url', 'files_path', 'show_auto', 'show_cmd', 'show_tmp_dir', 'default_session', 'javascript_session', 'browser_name', 'sessions']
        );

        $builder
            ->beforeNormalization()
                ->always()
                ->then(function (mixed $v) use ($renamedKeys): mixed {
                    if (!is_array($v)) {
                        return $v;
                    }

                    foreach ($renamedKeys as $driverType) {
                        if (!array_key_exists($driverType, $v)) {
                            continue;
                        }
                        /** @var array<string, array<string, mixed>> $sessions */
                        $sessions = is_array($v['sessions']) ? $v['sessions'] : [];
                        if (isset($sessions[$driverType])) {
                            continue;
                        }

                        $sessions[$driverType][$driverType] = $v[$driverType];
                        $v['sessions'] = $sessions;
                        unset($v[$driverType]);
                    }

                    return $v;
                })
            ->end()
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('mink_loader')->defaultNull()->end()
                ->scalarNode('base_url')->defaultNull()->end()
                ->scalarNode('files_path')->defaultNull()->end()
                ->booleanNode('show_auto')->defaultFalse()->end()
                ->scalarNode('show_cmd')->defaultNull()->end()
                ->scalarNode('show_tmp_dir')->defaultValue(sys_get_temp_dir())->end()
                ->scalarNode('default_session')->defaultNull()->info('Defaults to the first non-javascript session if any, or the first session otherwise')->end()
                ->scalarNode('javascript_session')->defaultNull()->info('Defaults to the first javascript session if any')->end()
                ->scalarNode('browser_name')->defaultValue('firefox')->end()
            ->end()
        ->end();

        /** @var ArrayNodeDefinition $sessionsBuilder */
        $sessionsBuilder = $builder
            ->children()
                ->arrayNode('sessions')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
        ;

        foreach ($this->driverFactories as $factory) {
            $factoryNode = $sessionsBuilder->children()->arrayNode($factory->getDriverName())->canBeUnset();

            $factory->configure($factoryNode);
        }

        $sessionsBuilder
            ->validate()
                ->ifTrue(function (mixed $v): bool {return is_array($v) && count($v) > 1; })
                ->thenInvalid('You cannot set multiple driver types for the same session')
            ->end()
            ->validate()
                ->ifTrue(function (mixed $v): bool {return is_array($v) && 0 === count($v); })
                ->thenInvalid('You must set a driver definition for the session.')
            ->end()
        ;
    }

    public function getConfigKey(): string
    {
        return 'mink';
    }

    public function initialize(ExtensionManager $extensionManager): void
    {
    }

    public function process(ContainerBuilder $container): void
    {
        $this->processSelectors($container);
    }

    private function loadMink(ContainerBuilder $container): void
    {
        $container->setDefinition(self::MINK_ID, new Definition('Behat\Mink\Mink'));
    }

    private function loadContextInitializer(ContainerBuilder $container): void
    {
        $definition = new Definition('Behat\MinkExtension\Context\Initializer\MinkAwareInitializer', [
            new Reference(self::MINK_ID),
            '%mink.parameters%',
        ]);
        $definition->addTag(ContextExtension::INITIALIZER_TAG, ['priority' => 0]);
        $container->setDefinition('mink.context_initializer', $definition);
    }

    private function loadSelectorsHandler(ContainerBuilder $container): void
    {
        $container->setDefinition(self::SELECTORS_HANDLER_ID, new Definition('Behat\Mink\Selector\SelectorsHandler'));

        $cssSelectorDefinition = new Definition('Behat\Mink\Selector\CssSelector');
        $cssSelectorDefinition->addTag(self::SELECTOR_TAG, ['alias' => 'css']);
        $container->setDefinition(self::SELECTOR_TAG.'.css', $cssSelectorDefinition);

        $namedSelectorDefinition = new Definition('Behat\Mink\Selector\NamedSelector');
        $namedSelectorDefinition->addTag(self::SELECTOR_TAG, ['alias' => 'named']);
        $container->setDefinition(self::SELECTOR_TAG.'.named', $namedSelectorDefinition);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function loadSessions(ContainerBuilder $container, array $config): void
    {
        /** @var string|null $defaultSession */
        $defaultSession = is_string($config['default_session']) ? $config['default_session'] : null;
        /** @var string|null $javascriptSession */
        $javascriptSession = is_string($config['javascript_session']) ? $config['javascript_session'] : null;
        /** @var string[] $javascriptSessions */
        $javascriptSessions = [];
        /** @var string[] $nonJavascriptSessions */
        $nonJavascriptSessions = [];

        $minkDefinition = $container->getDefinition(self::MINK_ID);

        /** @var array<string, array<string, mixed>> $sessions */
        $sessions = is_array($config['sessions']) ? $config['sessions'] : [];
        foreach ($sessions as $name => $session) {
            $driver = (string) key($session);
            $factory = $this->driverFactories[$driver];

            /** @var array<string, mixed> $driverConfig */
            $driverConfig = is_array($session[$driver]) ? $session[$driver] : [];
            $definition = new Definition('Behat\Mink\Session', [
                $factory->buildDriver($driverConfig),
                new Reference(self::SELECTORS_HANDLER_ID),
            ]);
            $minkDefinition->addMethodCall('registerSession', [$name, $definition]);

            if ($factory->supportsJavascript()) {
                $javascriptSessions[] = $name;
            } else {
                $nonJavascriptSessions[] = $name;
            }
        }

        if (null === $javascriptSession && !empty($javascriptSessions)) {
            $javascriptSession = $javascriptSessions[0];
        } elseif (null !== $javascriptSession && !in_array($javascriptSession, $javascriptSessions)) {
            throw new InvalidConfigurationException(sprintf('The javascript session must be one of the enabled javascript sessions (%s), but got %s', json_encode($javascriptSessions), $javascriptSession));
        }

        if (null === $defaultSession) {
            $defaultSession = !empty($nonJavascriptSessions) ? $nonJavascriptSessions[0] : $javascriptSessions[0];
        } elseif (!is_array($config['sessions']) || !isset($config['sessions'][$defaultSession])) {
            throw new InvalidConfigurationException(sprintf('The default session must be one of the enabled sessions, but got %s', $defaultSession));
        }

        $container->setParameter('mink.default_session', $defaultSession);
        $container->setParameter('mink.javascript_session', $javascriptSession);
        $container->setParameter('mink.available_javascript_sessions', $javascriptSessions);
    }

    private function loadSessionsListener(ContainerBuilder $container): void
    {
        $definition = new Definition('Behat\MinkExtension\Listener\SessionsListener', [
            new Reference(self::MINK_ID),
            '%mink.default_session%',
            '%mink.javascript_session%',
            '%mink.available_javascript_sessions%',
        ]);
        $definition->addTag(EventDispatcherExtension::SUBSCRIBER_TAG, ['priority' => 0]);
        $container->setDefinition('mink.listener.sessions', $definition);
    }

    private function loadFailureShowListener(ContainerBuilder $container): void
    {
        $definition = new Definition('Behat\MinkExtension\Listener\FailureShowListener', [
            new Reference(self::MINK_ID),
            '%mink.parameters%',
        ]);
        $definition->addTag(EventDispatcherExtension::SUBSCRIBER_TAG, ['priority' => 0]);
        $container->setDefinition('mink.listener.failure_show', $definition);
    }

    private function processSelectors(ContainerBuilder $container): void
    {
        $handlerDefinition = $container->getDefinition(self::SELECTORS_HANDLER_ID);

        foreach ($container->findTaggedServiceIds(self::SELECTOR_TAG) as $id => $tags) {
            foreach ($tags as $tag) {
                if (!is_array($tag) || !isset($tag['alias'])) {
                    throw new ProcessingException(sprintf('All `%s` tags should have an `alias` attribute, but `%s` service has none.', self::SELECTOR_TAG, $id));
                }
                $handlerDefinition->addMethodCall(
                    'registerSelector', [is_string($tag['alias']) ? $tag['alias'] : '', new Reference($id)]
                );
            }
        }
    }
}
