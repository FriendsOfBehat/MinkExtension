<?php

/*
 * This file is part of the Behat MinkExtension.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\MinkExtension\Context\Initializer;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;
use Behat\Mink\Mink;
use Behat\MinkExtension\Context\MinkAwareContext;

/**
 * Mink aware contexts initializer.
 * Sets Mink instance and parameters to the MinkAware contexts.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class MinkAwareInitializer implements ContextInitializer
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function __construct(
        private readonly Mink $mink,
        private readonly array $parameters,
    ) {
    }

    public function initializeContext(Context $context): void
    {
        if (!$context instanceof MinkAwareContext) {
            return;
        }

        $context->setMink($this->mink);
        $context->setMinkParameters($this->parameters);
    }
}
