#!/bin/bash
#
# Generate WP-CLI stubs.
#

PLUGIN_VERSION="2.2.0"

# Check plugin
if [ ! -r php/wp-cli.php ]; then
    echo "Please extract WP-CLI into the current directory!" 1>&2
    echo "git clone https://github.com/wp-cli/wp-cli.git" 1>&2
    exit 10
fi

rm composer.json composer.lock
rm -v php/WP_CLI/ComposerIO.php
rm -v php/WP_CLI/PackageManagerEventSubscriber.php

# Generate stubs
if [ ! -x vendor/bin/generate-stubs ]; then
    composer require --no-interaction --update-no-dev --prefer-dist giacocorsiglia/stubs-generator
fi
vendor/bin/generate-stubs --functions --classes --interfaces --traits --out=wp-cli-stubs-${PLUGIN_VERSION}.php ./php/
