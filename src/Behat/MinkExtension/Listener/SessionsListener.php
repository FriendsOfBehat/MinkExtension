<?php

/*
 * This file is part of the Behat MinkExtension.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\MinkExtension\Listener;

use Behat\Behat\EventDispatcher\Event\ExampleTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Gherkin\Node\TaggedNodeInterface;
use Behat\Mink\Mink;
use Behat\Testwork\EventDispatcher\Event\ExerciseCompleted;
use Behat\Testwork\ServiceContainer\Exception\ProcessingException;
use Behat\Testwork\Suite\Exception\SuiteConfigurationException;
use Behat\Testwork\Suite\Suite;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Mink sessions listener.
 * Listens Behat events and configures/stops Mink sessions.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * @internal
 */
final class SessionsListener implements EventSubscriberInterface
{
    private Mink $mink;
    private string $defaultSession;
    private ?string $javascriptSession;

    /**
     * @var string[] The available javascript sessions
     */
    private array $availableJavascriptSessions;

    /**
     * Initializes initializer.
     *
     * @param string[] $availableJavascriptSessions
     */
    public function __construct(Mink $mink, string $defaultSession, ?string $javascriptSession, array $availableJavascriptSessions = [])
    {
        $this->mink = $mink;
        $this->defaultSession = $defaultSession;
        $this->javascriptSession = $javascriptSession;
        $this->availableJavascriptSessions = $availableJavascriptSessions;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ScenarioTested::BEFORE => ['prepareDefaultMinkSession', 10],
            ExampleTested::BEFORE => ['prepareDefaultMinkSession', 10],
            ExerciseCompleted::AFTER => ['tearDownMinkSessions', -10],
        ];
    }

    /**
     * Configures default Mink session before each scenario.
     * Configuration is based on provided scenario tags:
     *
     * `@javascript` tagged scenarios will get `javascript_session` as default session
     * `@mink:CUSTOM_NAME tagged scenarios will get `CUSTOM_NAME` as default session
     * Other scenarios get `default_session` as default session
     *
     * `@insulated` tag will cause Mink to stop current sessions before scenario
     * instead of just soft-resetting them
     *
     * @throws ProcessingException when the @javascript tag is used without a javascript session
     */
    public function prepareDefaultMinkSession(ScenarioTested $event): void
    {
        $scenario = $event->getScenario();
        $feature = $event->getFeature();
        $session = null;

        $scenarioTags = $scenario instanceof TaggedNodeInterface ? $scenario->getTags() : [];
        // Behat 4 returns tags prefixed with "@"; normalize so comparisons work across versions.
        $tags = array_map(fn ($tag) => ltrim($tag, '@'), array_merge($feature->getTags(), $scenarioTags));
        foreach ($tags as $tag) {
            if ('javascript' === $tag) {
                $session = $this->getJavascriptSession($event->getSuite());
            } elseif (preg_match('/^mink\:(.+)/', $tag, $matches)) {
                $session = $matches[1];
            }
        }

        if (null === $session) {
            $session = $this->getDefaultSession($event->getSuite());
        }

        $isInsulated = in_array('insulated', $tags, true);
        if ($isInsulated) {
            $this->mink->stopSessions();
        } else {
            $this->mink->resetSessions();
        }

        $this->mink->setDefaultSessionName($session);
    }

    /**
     * Stops all started Mink sessions.
     */
    public function tearDownMinkSessions(): void
    {
        $this->mink->stopSessions();
    }

    private function getDefaultSession(Suite $suite): string
    {
        if (!$suite->hasSetting('mink_session')) {
            return $this->defaultSession;
        }

        $session = $suite->getSetting('mink_session');

        if (!is_string($session)) {
            throw new SuiteConfigurationException(sprintf('`mink_session` setting of the "%s" suite is expected to be a string, %s given.', $suite->getName(), gettype($session)), $suite->getName());
        }

        return $session;
    }

    private function getJavascriptSession(Suite $suite): string
    {
        if (!$suite->hasSetting('mink_javascript_session')) {
            if (null === $this->javascriptSession) {
                throw new ProcessingException('The @javascript tag cannot be used without enabling a javascript session');
            }

            return $this->javascriptSession;
        }

        $session = $suite->getSetting('mink_javascript_session');

        if (!is_string($session)) {
            throw new SuiteConfigurationException(sprintf('`mink_javascript_session` setting of the "%s" suite is expected to be a string, %s given.', $suite->getName(), gettype($session)), $suite->getName());
        }

        if (!in_array($session, $this->availableJavascriptSessions)) {
            throw new SuiteConfigurationException(sprintf('`mink_javascript_session` setting of the "%s" suite is not a javascript session. %s given but expected one of %s.', $suite->getName(), $session, implode(', ', $this->availableJavascriptSessions)), $suite->getName());
        }

        return $session;
    }
}
