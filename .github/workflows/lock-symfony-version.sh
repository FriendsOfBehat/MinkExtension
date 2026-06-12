#!/bin/bash

jq --indent 4 --arg version "$VERSION" '.require |= with_entries(.key as $k | if ($k | test("^symfony/")) then .value = $version else . end)' < composer.json > composer.json.tmp && mv composer.json.tmp composer.json
jq --indent 4 --arg version "$VERSION" '."require-dev" |= with_entries(.key as $k | if ($k | test("^symfony/")) then .value = $version else . end)' < composer.json > composer.json.tmp && mv composer.json.tmp composer.json
