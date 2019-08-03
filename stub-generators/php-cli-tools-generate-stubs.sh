#!/bin/bash
#
# Generate WP-CLI/php-cli-tools stubs.
#

PLUGIN_VERSION="0.11.11"

GENERATE_STUBS_COMMAND="vendor/bin/generate-stubs"

# Check plugin
if [ ! -r ./lib/cli/cli.php ]; then
    echo "Please clone php-cli-tools into the current directory!" 1>&2
    echo "git clone https://github.com/wp-cli/php-cli-tools.git" 1>&2
    exit 10
fi

# Generate stubs
if hash generate-stubs 2>/dev/null; then
    GENERATE_STUBS_COMMAND="generate-stubs"
elif hash generate-stubs.phar 2>/dev/null; then
    GENERATE_STUBS_COMMAND="generate-stubs.phar"
elif [ ! -x vendor/bin/generate-stubs ]; then
    composer require --no-interaction --update-no-dev --prefer-dist --ignore-platform-reqs \
        giacocorsiglia/stubs-generator
fi
"$GENERATE_STUBS_COMMAND" --functions --classes --out=php-cli-tools-stubs-${PLUGIN_VERSION}.php \
    ./lib/
