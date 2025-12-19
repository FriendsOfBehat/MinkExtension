# Mink Extension

This extension allows you to use the [Mink](http://mink.behat.org) browser emulator
abstraction with [Behat](https://github.com/Behat/Behat), a BDD tool for PHP.

Together, this allows you to do functional and acceptance testing of web applications. 
You can write behavior-driven tests for web applications using natural language 
descriptions and run them against different browsers and drivers without changing
your test code.

## Installation

1. Installation with Composer:

    ```bash
    $ composer require --dev friends-of-behat/mink-extension
    ```

2. Activate the extension by specifying its class in your `behat.yml`:

    ```yaml
    # behat.yml
    default:
      # ...
      extensions:
        Behat\MinkExtension:
          base_url:  'http://example.com'
          sessions:
            default:
              browserkit_http: ~
    ```

## Usage

After installing the extension, there are four usage options available:

1. Extending `Behat\MinkExtension\Context\RawMinkContext` in your feature suite.
   This will give you the ability to use a preconfigured `Mink` instance with some
   convenience methods:

   * `getSession($name = null)`
   * `assertSession($name = null)`

   `RawMinkContext` doesn't provide any hooks or definitions, so you can inherit from it
   in as many contexts as you want - you'll never get `RedundantStepException`.

2. Extending `Behat\MinkExtension\Context\MinkContext` with one of your contexts.
   Exactly like the previous option, but also provides lots of predefined step definitions out
   of the box. As this context provides step definitions and hooks, you can use it **only once**
   inside your feature context tree.

    ```php
    <?php

    use Behat\MinkExtension\Context\MinkContext;

    class FeatureContext extends MinkContext
    {
        /**
         * @Then /^I wait for the suggestion box to appear$/
         */
        public function iWaitForTheSuggestionBoxToAppear()
        {
            $this->getSession()->wait(5000, "$('.suggestions-results').children().length > 0");
        }
    }
    ```

> [!NOTE]
> Keep in mind, that you can not have multiple step definitions with the same regex.
> It will cause a `RedundantException`. So, you can inherit from `MinkContext`
> only with one of your context/subcontext classes.

3. Adding `Behat\MinkExtension\Context\MinkContext` as a context in your suite.
   Exactly like the previous option, but gives you the ability to keep your main context
   class clean.

    ```yaml
    default:
      suites:
        my_suite:
          contexts:
            - FeatureContext
            - Behat\MinkExtension\Context\MinkContext
    ```

> [!NOTE]
> Keep in mind, that you can not have multiple step definitions with the same regex.
> It will cause a `RedundantException`. So, you can inherit from `MinkContext`
> only with one of your context/subcontext classes.

4. Implementing `Behat\MinkExtension\Context\MinkAwareContext` with your context.

There are common things between these methods. In each of those, the target context will implement
`setMink(Mink $mink)` and `setMinkParameters(array $parameters)` methods. Those methods would
be automatically called *immediately after* each context creation before each scenario. And
this `$mink` instance will be preconfigured based on the settings you've provided in your
`behat.yml`.

## Configuration

MinkExtension comes with a flexible configuration system that gives you
the ability to configure Mink inside Behat from your `behat.yml` file.

### Sessions

You can register as many Mink sessions as you want. For each session, you
will need to choose the driver you want to use.

```yaml
default:
    extensions:
        Behat\MinkExtension:
            sessions:
                first_session:
                    selenium2: ~
                second_session:
                    browserkit_http: ~
                third_session:
                    selenium2: ~
```

MinkExtension will set the default Mink session for each scenario based on
the configuration settings `default_session` and `javascript_session`
and on scenario tags:

* A scenario tagged with `@mink:foo` will use `foo` as its default session;
* A scenario tagged with `@javascript` will use the `javascript` session as its default session;
* Other scenarios will use the default session.

The default session and the default `javascript` session can also be configured for
each suite:

```yaml
default:
    suites:
        first:
            mink_session: foo
            mink_javascript_session: sahi
```

If it is not configured explicitly, the `javascript` session is set to the first
session using a javascript driver in the order of the configuration (it would
be `first_session` in the example above as `selenium2` supports Javascript).
If it is not configured explicitly, the default session is set to the first
session using a non-javascript driver if any, or to the first javascript session
otherwise (it would be `second_session` above as `goutte` does not support
javascript).

### Drivers

Configuration settings depend on the Mink driver being used. Out of the box, MinkExtension can configure the following Mink drivers:

* **Symfony `BrowserKit` with Symfony `HttpClient`** - headless driver without JavaScript support. In order to use it, install [`behat/mink-browserkit-driver`](https://packagist.org/packages/behat/mink-browserkit-driver) through Composer. Then, modify your `behat.yml` profile:

    ```yaml
    default:
        extensions:
            Behat\MinkExtension:
                sessions:
                    my_session:
                        browserkit_http: ~
    ```

* **GoutteDriver** - headless driver without JavaScript support. In order to use it, install [`behat/mink-goutte-driver`](https://packagist.org/packages/behat/mink-goutte-driver) through Composer. Then, modify your `behat.yml` profile:

    ```yaml
    default:
        extensions:
            Behat\MinkExtension:
                sessions:
                    my_session:
                        goutte: ~
    ```

  **Tips: HTTPS and self-signed certificate**

  If you use Behat/Mink/Goutte to test your application, and want to test an
  application secured with HTTPS, but with a self-signed certificate, you can use
  the following parameters to avoid the validation error triggered by Guzzle:

  * For `Guzzle 4` or later:

      ```yaml
      default:
          extensions:
              Behat\MinkExtension:
                  sessions:
                      my_session:
                          goutte:
                              guzzle_parameters:
                                  verify: false
      ```

  * For `Guzzle 3` or earlier:

      ```yaml
      default:
          extensions:
              Behat\MinkExtension:
                  sessions:
                      my_session:
                          goutte:
                              guzzle_parameters:
                                  ssl.certificate_authority: false
      ```

* **Selenium2Driver** - javascript driver. In order to use it, install [`behat/mink-selenium2-driver`](https://packagist.org/packages/behat/mink-selenium2-driver) through Composer. Then, modify your `behat.yml` profile:

    ```yaml
    default:
        extensions:
            Behat\MinkExtension:
                sessions:
                    my_session:
                        selenium2: ~
    ```

* **SauceLabsDriver** - special flavor of the Selenium2Driver configured to use the
  selenium2 hosted installation of saucelabs.com. This uses Selenium2Driver, so make sure you have [`behat/mink-selenium2-driver`](https://packagist.org/packages/behat/mink-selenium2-driver) installed. Then, modify your `behat.yml` profile:

    ```yaml
    default:
        extensions:
            Behat\MinkExtension:
                sessions:
                    my_session:
                        sauce_labs: ~
    ```

* **BrowserStackDriver** - special flavor of the Selenium2Driver configured to use the
  selenium2 hosted installation of browserstack.com. This uses Selenium2Driver, so make sure you have [`behat/mink-selenium2-driver`](https://packagist.org/packages/behat/mink-selenium2-driver) installed. Then, modify your `behat.yml` profile:

    ```yaml
    default:
        extensions:
            Behat\MinkExtension:
                sessions:
                    my_session:
                        browser_stack: ~
    ```

* **SeleniumDriver** - javascript driver. In order to use it, install [`behat/mink-selenium-driver`](https://packagist.org/packages/behat/mink-selenium-driver) through Composer. Then, modify your `behat.yml` profile:

    ```yaml
    default:
        extensions:
            Behat\MinkExtension:
                sessions:
                    my_session:
                        selenium: ~
    ```

* **SahiDriver** - javascript driver. In order to use it, install [`behat/mink-sahi-driver`](https://packagist.org/packages/behat/mink-sahi-driver) through Composer. Then, modify your `behat.yml` profile:

    ```yaml
    default:
        extensions:
            Behat\MinkExtension:
                sessions:
                    my_session:
                        sahi: ~
    ```

* **ZombieDriver** - zombie.js javascript headless driver. In order to use it, install [`behat/mink-zombie-driver`](https://packagist.org/packages/behat/mink-zombie-driver) through Composer. Then, modify your `behat.yml` profile:

    ```yaml
    default:
        extensions:
            Behat\MinkExtension:
                sessions:
                    default:
                        zombie:
                            # Specify the path to the node_modules directory.
                            node_modules_path: /usr/local/lib/node_modules/
    ```

> [!NOTE]
> All drivers share the same API, which means that you could use multiple drivers
> for the same suite - whichever one fits your needs for concrete scenarios. Don't
> try to stick to a single driver as there's simply no universal solution - every
> driver has its pros and cons.

### Additional Parameters

There's other useful parameters, that you can use to configure your suite:

* `base_url` - if you're using relative paths in your `*.feature` files
  (and you should), then this option will define which url to use as a basename
  for them.
* `files_path` - there's a special step definition for file upload inputs
  usage. You can use relative paths in those steps. `files_path` defines
  the base path in which Mink should search for those relative files.
* `show_cmd` - there's a special definition in MinkExtension, that saves
  the currently opened page into a temporary file and opens it with some browser
  utility (for debugging). This option defines the command to be used for opening.
  For example: `show_cmd: 'firefox %s'`.
* `show_tmp_dir` - the temporary folder used to show the opened page (defaults
  to the system temp dir)
* `show_auto` - Whether the opened page should be shown automatically when
  a step fails.
* `browser_name` - meta-option, that defines which browser to use for Sahi,
  Selenium and Selenium2 drivers.
* `default_session` - defines the default session (driver) to be used for all
  untagged scenarios. This could be any enabled session name.
* `javascript_session` - defines the javascript session (driver) (the one, which
  will be used for `@javascript` tagged scenarios). This could be any enabled session
  name.
* `mink_loader` - path to a file loaded to make Mink available (useful when
  using the PHAR archive for Mink, useless when using Composer)

## Adding your own or other drivers

If you have another Mink driver implementation that you would like to use, 
you can create your own `DriverFactory` implementation. Then, create a Behat
extension class and use it to register your driver factory with this extension
here along the lines of:

```php
<?php

namespace My\Mink\DriverPackage;

use Behat\MinkExtension\ServiceContainer\MinkExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;

class MyMinkDriverExtension implements ExtensionInterface
{
    // ... other ExtensionInterface methods omitted for brevity 

    public function initialize(ExtensionManager $extensionManager): void
    {
        if (null !== $minkExtension = $extensionManager->getExtension('mink')) {
            /* @var $minkExtension MinkExtension */
            $minkExtension->registerDriverFactory(new MyDriverFactory());
        }
    }
}
```

Your driver factory defines the driver name, which can be used to configure
_your_ driver in the `behat.yml` file just like the other drivers shown above.
