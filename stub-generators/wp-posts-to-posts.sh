#!/bin/bash
#
# Generate Posts 2 Posts stubs.
#

PLUGIN_VERSION="1.6.5"

GENERATE_STUBS_COMMAND="vendor/bin/generate-stubs"

# Check plugin
if [ ! -r ./posts-to-posts.php ]; then
    echo "Please extract Posts 2 Posts into the current directory!" 1>&2
    echo "wget https://downloads.wordpress.org/plugin/posts-to-posts.${PLUGIN_VERSION}.zip && unzip posts-to-posts.${PLUGIN_VERSION}.zip" 1>&2
    exit 10
fi

rm vendor/scribu/scb-framework/load.php
sed -e '/^\/\/ WP/,$d' -i vendor/scribu/lib-posts-to-posts/item-user.php

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

"$GENERATE_STUBS_COMMAND" --functions --classes --interfaces --traits --out=wp-posts-to-posts-${PLUGIN_VERSION}.php \
    vendor/scribu/scb-framework vendor/scribu/lib-posts-to-posts/ posts-to-posts.php
