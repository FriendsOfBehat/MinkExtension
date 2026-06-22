# Upgrade notes for FriendsOfBehat/MinkExtension

This document summarizes the changes relevant for users when upgrading to new versions.

# Upgrade to 2.8

## Soft `@final` and `@internal` declarations added

The classes `FailureShowListener`, `SessionsListener` and `MinkExtension` have been marked as `@final`. They will become `final` classes in the next major release and you will no longer be able to use them by inheritance (https://github.com/FriendsOfBehat/MinkExtension/pull/41).

Additionally, the two listener classes have been marked as `@internal`. Starting with the next major version, their API may change at any time without further notice.

## Deprecated drivers

Support for the following drivers has been deprecated, since the underlying driver implementations have been abandoned:

- GoutteDriver
- SeleniumDriver
- SahiDriver
- ZombieDriver

The corresponding `Factory` classes will trigger a deprecation notice when they are used to build the driver, for example
by using `goutte` as the driver identifier in the `behat.yml` configuration file (https://github.com/FriendsOfBehat/MinkExtension/pull/39/).

These deprecations will be reported if running Behat with the `--print-behat-deprecations` option,
and will cause the build to fail if running with the `--fail-on-behat-deprecations` option. These options can also be set on the `TesterOptions` in a behat config file.
