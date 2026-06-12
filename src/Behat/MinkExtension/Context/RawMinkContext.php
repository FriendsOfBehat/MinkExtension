<?php

/*
 * This file is part of the Behat MinkExtension.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\MinkExtension\Context;

use Behat\Mink\Mink;
use Behat\Mink\Session;
use Behat\Mink\WebAssert;

/**
 * Raw Mink context for Behat BDD tool.
 * Provides raw Mink integration (without step definitions) and web assertions.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class RawMinkContext implements MinkAwareContext
{
    private ?Mink $mink = null;

    /** @var array<string, mixed> */
    private array $minkParameters = [];

    /**
     * Sets Mink instance.
     *
     * @param Mink $mink Mink session manager
     */
    public function setMink(Mink $mink): void
    {
        $this->mink = $mink;
    }

    /**
     * Returns Mink instance.
     */
    public function getMink(): Mink
    {
        if (null === $this->mink) {
            throw new \RuntimeException('Mink instance has not been set on Mink context class. Have you enabled the Mink Extension?');
        }

        return $this->mink;
    }

    /**
     * Returns the parameters provided for Mink.
     *
     * @return array<string, mixed>
     */
    public function getMinkParameters(): array
    {
        return $this->minkParameters;
    }

    /**
     * Sets parameters provided for Mink.
     *
     * @param array<string, mixed> $parameters
     */
    public function setMinkParameters(array $parameters): void
    {
        $this->minkParameters = $parameters;
    }

    /**
     * Returns specific mink parameter.
     */
    public function getMinkParameter(string $name): mixed
    {
        return isset($this->minkParameters[$name]) ? $this->minkParameters[$name] : null;
    }

    /**
     * Applies the given parameter to the Mink configuration. Consider that all parameters get reset for each
     * feature context.
     *
     * @param string $name  The key of the parameter
     * @param mixed  $value The value of the parameter
     */
    public function setMinkParameter(string $name, mixed $value): void
    {
        $this->minkParameters[$name] = $value;
    }

    /**
     * Returns Mink session.
     *
     * @param string|null $name name of the session OR active session will be used
     */
    public function getSession(?string $name = null): Session
    {
        return $this->getMink()->getSession($name);
    }

    /**
     * Returns Mink session assertion tool.
     *
     * @param string|null $name name of the session OR active session will be used
     */
    public function assertSession(?string $name = null): WebAssert
    {
        return $this->getMink()->assertSession($name);
    }

    /**
     * Visits provided relative path using provided or default session.
     */
    public function visitPath(string $path, ?string $sessionName = null): void
    {
        $this->getSession($sessionName)->visit($this->locatePath($path));
    }

    /**
     * Locates url, based on provided path.
     * Override to provide custom routing mechanism.
     */
    public function locatePath(string $path): string
    {
        $baseUrl = $this->getMinkParameter('base_url');
        $startUrl = rtrim(is_string($baseUrl) ? $baseUrl : '', '/').'/';

        return 0 !== strpos($path, 'http') ? $startUrl.ltrim($path, '/') : $path;
    }

    /**
     * Save a screenshot of the current window to the file system.
     *
     * @param string $filename Desired filename, defaults to
     *                         <browser_name>_<ISO 8601 date>_<randomId>.png
     * @param string $filepath Desired filepath, defaults to
     *                         upload_tmp_dir, falls back to sys_get_temp_dir()
     */
    public function saveScreenshot(?string $filename = null, ?string $filepath = null): void
    {
        // Under Cygwin, uniqid with more_entropy must be set to true.
        // No effect in other environments.
        $browserName = $this->getMinkParameter('browser_name');
        $filename = $filename ?: sprintf('%s_%s_%s.%s', is_string($browserName) ? $browserName : '', date('c'), uniqid('', true), 'png');
        $filepath = $filepath ?: (ini_get('upload_tmp_dir') ?: sys_get_temp_dir());
        file_put_contents($filepath.'/'.$filename, $this->getSession()->getScreenshot());
    }
}
