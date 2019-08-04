#!/bin/bash
#
# Generate Polylang stubs.
#

PLUGIN_VERSION="2.6.2"

# Check plugin
if ! grep -q 'Plugin Name:\s\+Polylang' ./polylang.php 2>/dev/null; then
    echo "Please extract Polylang into the current directory!" 1>&2
    echo "git clone https://github.com/polylang/polylang.git" 1>&2
    exit 10
fi

rm -v include/functions.php

# Generate stubs
if [ ! -x vendor/bin/generate-stubs ]; then
    composer require --no-interaction --update-no-dev --prefer-dist giacocorsiglia/stubs-generator
fi
vendor/bin/generate-stubs --functions --classes --interfaces --traits --out=polylang-stubs-${PLUGIN_VERSION}.php \
    ./admin/ ./include/ ./lingotek/ ./settings/
