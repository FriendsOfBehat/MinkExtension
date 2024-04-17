<?php

namespace Behat\MinkExtension\ServiceContainer\Driver;

/**
 * @internal
 */
trait EnvironmentCapabilities
{
    private function guessEnvironmentCapabilities(): array
    {
        switch (true) {
            case (bool)getenv('TRAVIS_JOB_NUMBER'):
                return [
                    'tunnel-identifier' => getenv('TRAVIS_JOB_NUMBER'),
                    'build' => getenv('TRAVIS_BUILD_NUMBER'),
                    'tags' => [
                        'Travis-CI',
                        'PHP ' . PHP_VERSION,
                    ],
                ];

            case (bool)getenv('JENKINS_HOME'):
                return [
                    'tunnel-identifier' => getenv('JOB_NAME'),
                    'build' => getenv('BUILD_NUMBER'),
                    'tags' => [
                        'Jenkins',
                        'PHP ' . PHP_VERSION,
                        getenv('BUILD_TAG'),
                    ],
                ];

            default:
                return [
                    'tags' => [
                        php_uname('n'),
                        'PHP ' . PHP_VERSION,
                    ],
                ];
        }
    }
}
