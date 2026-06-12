<?php

namespace Behat\MinkExtension\ServiceContainer\Driver;

/**
 * @internal
 */
trait EnvironmentCapabilities
{
    /**
     * @return array<string, mixed>
     */
    private function guessEnvironmentCapabilities(): array
    {
        $travisJobNumber = getenv('TRAVIS_JOB_NUMBER');
        $jenkinsHome = getenv('JENKINS_HOME');

        switch (true) {
            case (bool) $travisJobNumber:
                return [
                    'tunnel-identifier' => $travisJobNumber,
                    'build' => getenv('TRAVIS_BUILD_NUMBER'),
                    'tags' => [
                        'Travis-CI',
                        'PHP '.PHP_VERSION,
                    ],
                ];

            case (bool) $jenkinsHome:
                return [
                    'tunnel-identifier' => getenv('JOB_NAME'),
                    'build' => getenv('BUILD_NUMBER'),
                    'tags' => [
                        'Jenkins',
                        'PHP '.PHP_VERSION,
                        getenv('BUILD_TAG'),
                    ],
                ];

            default:
                return [
                    'tags' => [
                        php_uname('n'),
                        'PHP '.PHP_VERSION,
                    ],
                ];
        }
    }
}
