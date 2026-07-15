# Upgrade notes for FriendsOfBehat/MinkExtension

This document summarizes the changes relevant for users when upgrading to new versions.

# Upgrade to 3.0

## PHP, Symfony and Behat minimum versions raised

- PHP `^7.4 || ^8` → `^8.3`
- `symfony/config` `^4.4 || ^5.0 || ^6.0 || ^7.0` → `^7.4 || ^8.0`
- `behat/behat` `^3.0.5` → `^3.32 || ^4.0`
- `behat/mink` `^1.5` → `^1.11`

## Abandoned driver factories removed

The following driver factories have been removed, together with the driver identifiers you used to select
them in the configuration, because the underlying driver implementations are abandoned:

| Removed factory   | Driver identifier | Underlying driver |
|-------------------|-------------------|-------------------|
| `GoutteFactory`   | `goutte`          | GoutteDriver      |
| `SeleniumFactory` | `selenium`        | SeleniumDriver    |
| `SahiFactory`     | `sahi`            | SahiDriver        |
| `ZombieFactory`   | `zombie`          | ZombieDriver      |

If your `behat.php` (or legacy `behat.yml`) references any of these identifiers under `Behat\MinkExtension`,
the configuration will no longer be valid. Switch to an actively maintained driver, such as
`browserkit_http` (via `behat/mink-browserkit-driver`) for headless HTTP testing, or `selenium2`/`webdriver`
based drivers for browser testing.

## Behat 4 compatibility

Step definitions in `MinkContext` now use PHP 8 attributes (`#[\Behat\Step\Given(...)]` etc.) instead of
docblock annotations. Behat 3.32+ and Behat 4.x are both supported.

Behat 4 no longer supports the classic `behat.yml` configuration format and requires PHP-based
configuration files (`behat.php`). On Behat 3.x, `behat.yml` still works, but the format is deprecated in
favour of `behat.php`. The documentation examples now use the `behat.php` format. If you are still using
`behat.yml`, convert it with Behat's built-in converter (`vendor/bin/behat --convert-config`) before
upgrading to Behat 4.

## `FailureShowListener`, `SessionsListener` and `MinkExtension` are now `final`

These classes are now `final` and can no longer be extended. If you relied on inheritance, use composition
instead (wrap or decorate them, or register your own service).

Additionally, `FailureShowListener` and `SessionsListener` are now marked as `@internal`. Their API may
change at any time without further notice.
