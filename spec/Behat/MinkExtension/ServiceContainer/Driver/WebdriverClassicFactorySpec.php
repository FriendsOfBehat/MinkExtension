<?php

namespace spec\Behat\MinkExtension\ServiceContainer\Driver;

use PhpSpec\ObjectBehavior;
use Behat\MinkExtension\ServiceContainer\Driver\DriverFactory;

class WebdriverClassicFactorySpec extends ObjectBehavior
{
    public function it_is_a_driver_factory(): void
    {
        $this->shouldHaveType(DriverFactory::class);
    }

    public function it_is_named_webdriver_classic(): void
    {
        $this->getDriverName()->shouldReturn('webdriver-classic');
    }

    public function it_supports_javascript(): void
    {
        $this->supportsJavascript()->shouldBe(true);
    }
}
