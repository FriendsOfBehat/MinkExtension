<?php

declare(strict_types=1);

use Behat\Config\Config;
use Behat\Config\Extension;
use Behat\Config\Profile;
use Behat\Config\Suite;

return (new Config())
    ->withProfile(
        (new Profile('default'))
            ->withSuite(
                (new Suite('default'))
                    ->withPaths('%paths.base%/features')
                    ->withContexts(\Behat\MinkExtension\Context\MinkContext::class)
            )
            ->withExtension(
                new Extension(\Behat\MinkExtension\ServiceContainer\MinkExtension::class, [
                    'base_url' => 'http://en.wikipedia.org/',
                    'sessions' => [
                        'default' => [
                            'browserkit_http' => null,
                        ],
                    ],
                ])
            )
    );
