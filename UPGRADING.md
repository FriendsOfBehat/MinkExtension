# Upgrade notes for FriendsOfBehat/MinkExtension

This document summarizes the changes relevant for users when upgrading to new versions.

# Upgrade to 2.8

## Soft `@final` declarations added

The classes `FailureShowListener`, `SessionsListener` and `MinkExtension` have been marked as `@final`.

They will become `final` classes in the next major release and you will no longer be able to use them
by inheritance (https://github.com/FriendsOfBehat/MinkExtension/pull/41).
