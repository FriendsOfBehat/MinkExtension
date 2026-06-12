<?php

namespace spec\Behat\MinkExtension\ServiceContainer\Driver;

use PhpSpec\ObjectBehavior;

class SeleniumFactorySpec extends ObjectBehavior
{
    public function it_is_a_driver_factory()
    {
        $this->shouldHaveType('Behat\MinkExtension\ServiceContainer\Driver\DriverFactory');
    }

    public function it_is_named_selenium()
    {
        $this->getDriverName()->shouldReturn('selenium');
    }

    public function it_supports_javascript()
    {
        $this->supportsJavascript()->shouldBe(true);
    }
}
