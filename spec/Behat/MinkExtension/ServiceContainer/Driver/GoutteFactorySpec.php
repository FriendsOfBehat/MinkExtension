<?php

namespace spec\Behat\MinkExtension\ServiceContainer\Driver;

use PhpSpec\ObjectBehavior;

class GoutteFactorySpec extends ObjectBehavior
{
    public function it_is_a_driver_factory()
    {
        $this->shouldHaveType('Behat\MinkExtension\ServiceContainer\Driver\DriverFactory');
    }

    public function it_is_named_goutte()
    {
        $this->getDriverName()->shouldReturn('goutte');
    }

    public function it_does_not_support_javascript()
    {
        $this->supportsJavascript()->shouldBe(false);
    }
}
