<?php

namespace spec\Behat\MinkExtension\ServiceContainer;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;

class MinkExtensionSpec extends ObjectBehavior
{
    function it_is_a_testwork_extension()
    {
        $this->shouldHaveType('Behat\Testwork\ServiceContainer\Extension');
    }

    function it_is_named_mink()
    {
        $this->getConfigKey()->shouldReturn('mink');
    }

    function it_rejects_a_non_array_config_with_a_meaningful_exception()
    {
        $tree = $this->buildConfigTree();

        try {
            (new Processor())->process($tree, array(null));
            throw new \Exception('Expected an InvalidConfigurationException to be thrown.');
        } catch (InvalidConfigurationException $e) {
            // expected: a misconfigured extension (e.g. "MinkExtension: ~") must not
            // trigger a fatal TypeError during normalization
        }
    }

    function it_still_moves_shortcut_driver_keys_into_sessions_for_array_configs()
    {
        $tree = $this->buildConfigTree();

        $config = (new Processor())->process($tree, array(array('goutte' => array())));

        if (!array_key_exists('goutte', $config['sessions']['goutte'])) {
            throw new \Exception('Expected the "goutte" shortcut key to be moved under sessions.goutte.goutte.');
        }
    }

    private function buildConfigTree()
    {
        $treeBuilder = new TreeBuilder('mink', 'array');
        $this->configure($treeBuilder->getRootNode());

        return $treeBuilder->buildTree();
    }
}
