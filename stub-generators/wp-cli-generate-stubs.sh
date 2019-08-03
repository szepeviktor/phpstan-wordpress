#!/bin/bash
#
# Generate WP-CLI stubs.
#

PLUGIN_VERSION="2.2.0"

GENERATE_STUBS_COMMAND="vendor/bin/generate-stubs"

# Check plugin
if [ ! -r php/wp-cli.php ]; then
    echo "Please extract WP-CLI into the current directory!" 1>&2
    echo "git clone https://github.com/wp-cli/wp-cli.git" 1>&2
    exit 10
fi

rm -v php/WP_CLI/ComposerIO.php
rm -v php/WP_CLI/PackageManagerEventSubscriber.php

# Generate stubs
if hash generate-stubs 2>/dev/null; then
    GENERATE_STUBS_COMMAND="generate-stubs"
elif hash generate-stubs.phar 2>/dev/null; then
    GENERATE_STUBS_COMMAND="generate-stubs.phar"
elif [ ! -x vendor/bin/generate-stubs ]; then
    rm composer.json composer.lock
    composer require --no-interaction --update-no-dev --prefer-dist --ignore-platform-reqs \
        giacocorsiglia/stubs-generator
fi
# If WP-CLI is a dependency remove "./vendor/wp-cli/php-cli-tools/lib/" path
"$GENERATE_STUBS_COMMAND" --functions --classes --interfaces --traits --out=wp-cli-stubs-${PLUGIN_VERSION}.php \
    ./php/ ./vendor/wp-cli/php-cli-tools/lib/
