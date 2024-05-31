<?php

/*
 * This file is part of the Behat MinkExtension.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\MinkExtension\ServiceContainer\Driver;

use GuzzleHttp\ClientInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Christophe Coevoet <stof@notk.org>
 */
class GoutteFactory implements DriverFactory
{
    /**
     * {@inheritdoc}
     */
    public function getDriverName()
    {
        return 'goutte';
    }

    /**
     * {@inheritdoc}
     */
    public function supportsJavascript()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->arrayNode('server_parameters')
                    ->useAttributeAsKey('key')
                    ->prototype('variable')->end()
                ->end()
                ->arrayNode('guzzle_parameters')
                    ->useAttributeAsKey('key')
                    ->prototype('variable')->end()
                    ->info(
                        "For Goutte 1.x, these are the second argument of the Guzzle3 client constructor.\n".
                        'For Goutte 2.x, these are the elements passed in the "defaults" key of the Guzzle4 config.'
                    )
                ->end()
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildDriver(array $config)
    {
        if (!class_exists('Behat\Mink\Driver\GoutteDriver')) {
            throw new \RuntimeException(
                'Install MinkGoutteDriver in order to use goutte driver.'
            );
        }

        $clientArguments = array(
            $config['server_parameters'],
        );
        $guzzleClient = null;

        if ($this->isGoutte4()) {
            $clientArguments = array();

            if (class_exists('Symfony\Component\HttpClient\HttpClient')) {
                $httpClient = new Definition('Symfony\Component\HttpClient\HttpClient');
                $httpClient->setFactory('Symfony\Component\HttpClient\HttpClient::create');
                $httpClient->setArgument(0, $config['server_parameters']);
                $clientArguments = array($httpClient);
            }
        } elseif ($this->isGoutte1()) {
            $guzzleClient = $this->buildGuzzle3Client($config['guzzle_parameters']);
        } elseif ($this->isGuzzle6()) {
            $guzzleClient = $this->buildGuzzle6Client($config['guzzle_parameters']);
        } else {
            $guzzleClient = $this->buildGuzzle4Client($config['guzzle_parameters']);
        }

        $clientDefinition = new Definition('Behat\Mink\Driver\Goutte\Client', $clientArguments);

        if (null !== $guzzleClient) {
            $clientDefinition->addMethodCall('setClient', array($guzzleClient));
        }

        return new Definition('Behat\Mink\Driver\GoutteDriver', array(
            $clientDefinition,
        ));
    }

    private function buildGuzzle6Client(array $parameters)
    {
        // Force the parameters set by default in Goutte to reproduce its behavior
        $parameters['allow_redirects'] = false;
        $parameters['cookies'] = true;

        return new Definition('GuzzleHttp\Client', array($parameters));
    }

    private function buildGuzzle4Client(array $parameters)
    {
        // Force the parameters set by default in Goutte to reproduce its behavior
        $parameters['allow_redirects'] = false;
        $parameters['cookies'] = true;

        return new Definition('GuzzleHttp\Client', array(array('defaults' => $parameters)));
    }

    private function buildGuzzle3Client(array $parameters)
    {
        // Force the parameters set by default in Goutte to reproduce its behavior
        $parameters['redirect.disable'] = true;

        return new Definition('Guzzle\Http\Client', array(null, $parameters));
    }

    private function isGoutte4()
    {
        $client = 'Goutte\Client';

        return class_exists($client) && is_a($client, 'Symfony\Component\BrowserKit\HttpBrowser', true);
    }

    private function isGoutte1()
    {
        $refl = new \ReflectionParameter(array('Goutte\Client', 'setClient'), 0);

        $type = $refl->getType();
        if ($type instanceof \ReflectionNamedType && 'Guzzle\Http\ClientInterface' === $type->getName()) {
            return true;
        }

        return false;
    }

    private function isGuzzle6()
    {
        if (!interface_exists(ClientInterface::class)) {
            return false;
        }
        $rc = new \ReflectionClass(ClientInterface::class);
        // This constant was removed in Guzzle 7.
        if ($rc->hasConstant('VERSION')) {
            return version_compare(ClientInterface::VERSION, '6.0.0', '>=');
        }
        // This constant was added in Guzzle 7.
        if ($rc->hasConstant('MAJOR_VERSION')) {
            ClientInterface::MAJOR_VERSION >= 6;
        }
        return false;
    }
}
