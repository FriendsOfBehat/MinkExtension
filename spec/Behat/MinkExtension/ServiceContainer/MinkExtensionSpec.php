<?php

namespace spec\Behat\MinkExtension\ServiceContainer;

use Behat\MinkExtension\ServiceContainer\MinkExtension;
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

        $this->beAnInstanceOf(Processor::class);
        $this->shouldThrow(InvalidConfigurationException::class)
            ->duringProcess($tree, array(null));
    }

    function it_still_moves_shortcut_driver_keys_into_sessions_for_array_configs()
    {
        $tree = $this->buildConfigTree();

        $this->beAnInstanceOf(Processor::class);
        $config = $this->process($tree, array(array('browserkit_http' => array())));

        $config['sessions']['browserkit_http']->shouldHaveKey('browserkit_http');
    }

    private function buildConfigTree()
    {
        $treeBuilder = new TreeBuilder('mink', 'array');
        (new MinkExtension())->configure($treeBuilder->getRootNode());

        return $treeBuilder->buildTree();
    }
}
