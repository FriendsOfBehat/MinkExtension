<?php

namespace spec\Behat\MinkExtension\ServiceContainer\Driver;

use Behat\MinkExtension\ServiceContainer\Driver\DriverFactory;
use PhpSpec\ObjectBehavior;

class BrowserKitFactorySpec extends ObjectBehavior
{
    public function it_is_a_driver_factory()
    {
        $this->shouldHaveType(DriverFactory::class);
    }

    public function it_is_named_browserkit()
    {
        $this->getDriverName()->shouldReturn('browserkit_http');
    }

    public function it_does_not_support_javascript()
    {
        $this->supportsJavascript()->shouldBe(false);
    }
}
