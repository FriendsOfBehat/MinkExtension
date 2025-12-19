# Mink Extension for Behat

This repository provides an integration of the [Mink browser emulator abstraction library](https://github.com/minkphp/Mink) with [Behat](https://github.com/Behat/Behat).

It started as a fork of https://github.com/Behat/MinkExtension, which has been abandoned in early 2024.

## What does it do?

This Behat extension is an integration layer between Behat and Mink. It enables functional and acceptance testing of web applications. That way, you can write behavior-driven tests for web applications using natural language descriptions and run them against different browsers and drivers without changing your test code.

It provides:

- **Context classes:** (`MinkAwareContext`, `RawMinkContext`, `MinkContext`) with convenience methods and predefined step definitions that make it easier to use Mink in your Behat scenarios and contexts 
- **Mink session configuration:** You can define multiple Mink sessions and the browser drivers to be used in your `behat.yml` file. In your Behat scenarios, the session to be used can be selected based on tags (`@javascript`, `@mink:session_name`).
- **Support for multiple Mink drivers:** Including Symfony BrowserKit, Selenium2, BrowserStack and many more. Additionally, third-party extensions can register their own drivers as well.
- **Configuration options** for base URLs, file paths, browser selection, and driver-specific settings

For detailed documentation, see [doc/index.md](doc/index.md). 
