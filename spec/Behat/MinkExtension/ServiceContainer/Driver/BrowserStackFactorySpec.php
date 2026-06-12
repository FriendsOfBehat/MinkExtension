<?php

namespace spec\Behat\MinkExtension\ServiceContainer\Driver;

use PhpSpec\ObjectBehavior;

class BrowserStackFactorySpec extends ObjectBehavior
{
    public function it_is_a_driver_factory()
    {
        $this->shouldHaveType('Behat\MinkExtension\ServiceContainer\Driver\DriverFactory');
    }

    public function it_is_named_browser_stack()
    {
        $this->getDriverName()->shouldReturn('browser_stack');
    }

    public function it_supports_javascript()
    {
        $this->supportsJavascript()->shouldBe(true);
    }
}
