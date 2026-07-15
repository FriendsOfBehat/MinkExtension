# Upgrade notes for FriendsOfBehat/MinkExtension

This document summarizes the changes relevant for users when upgrading to new versions.

# Upgrade to 3.0

## PHP, Symfony and Behat minimum versions raised

- PHP `^7.4 || ^8` → `^8.3`
- `symfony/config` `^4.4 || ^5.0 || ^6.0 || ^7.0` → `^7.4 || ^8.0`
- `behat/behat` `^3.0.5` → `^3.32 || ^4.0`
- `behat/mink` `^1.5` → `^1.11`

## Abandoned driver factories removed

The `GoutteFactory`, `SeleniumFactory`, `SahiFactory` and `ZombieFactory` classes have been removed.
These were deprecated in 2.8.0 and the underlying driver implementations are abandoned.

Switch to `browserkit_http` (via `behat/mink-browserkit-driver`) or another actively maintained driver.

## Behat 4 compatibility

Step definitions in `MinkContext` now use PHP 8 attributes (`#[\Behat\Step\Given(...)]` etc.) instead of
docblock annotations. Behat 3.32+ and Behat 4.x are both supported.

Behat 4 no longer supports the classic `behat.yml` configuration format and requires PHP-based
configuration files (`behat.php`). On Behat 3.x, `behat.yml` still works, but the format is deprecated in
favour of `behat.php`. The documentation examples now use the `behat.php` format. If you are still using
`behat.yml`, convert it with Behat's built-in converter (`vendor/bin/behat --convert-config`) before
upgrading to Behat 4.

## `FailureShowListener`, `SessionsListener` and `MinkExtension` are now `final`

These classes were marked as soft `@final` in 2.8.0 and are now actually `final`.
You can no longer extend them. If you relied on inheritance, use composition instead
(wrap or decorate them, or register your own service).

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

Note, however, that Behat currently does not have a built-in mechanism to collect such deprecation notices and display
them in a user-friendly way.
