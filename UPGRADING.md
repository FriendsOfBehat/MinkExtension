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

Note, however, that Behat currently does not have a built-in mechanism to collect such deprecation notices and display
them in a user-friendly way.

## Soft `@return` types added

`@return` type hints have been added to the `MinkAwareContext` and `DriverFactory` interfaces and to all methods
in `MinkContext` and `RawMinkContext`.

If you implement or extend these classes or any of the drivers shipped here, make sure to add _real_ corresponding
return types to your implementations now. Signatures will be changed to include return types in the next major version.
