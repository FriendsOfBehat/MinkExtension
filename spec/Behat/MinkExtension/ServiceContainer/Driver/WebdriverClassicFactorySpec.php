<?php

namespace spec\Behat\MinkExtension\ServiceContainer\Driver;

use Behat\MinkExtension\ServiceContainer\Driver\DriverFactory;
use PhpSpec\ObjectBehavior;

class WebdriverClassicFactorySpec extends ObjectBehavior
{
    public function it_is_a_driver_factory(): void
    {
        $this->shouldHaveType(DriverFactory::class);
    }

    public function it_is_named_webdriver_classic(): void
    {
        $this->getDriverName()->shouldReturn('webdriver_classic');
    }

    public function it_supports_javascript(): void
    {
        $this->supportsJavascript()->shouldBe(true);
    }
}
