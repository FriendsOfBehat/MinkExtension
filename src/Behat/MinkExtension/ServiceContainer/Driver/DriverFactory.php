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

/**
 * @author Christophe Coevoet <stof@notk.org>
 */
interface DriverFactory
{
    public function getDriverName(): string;

    public function supportsJavascript(): bool;

    public function configure(ArrayNodeDefinition $builder): void;

    /**
     * @param array<string, mixed> $config
     */
    public function buildDriver(array $config): Definition;
}
