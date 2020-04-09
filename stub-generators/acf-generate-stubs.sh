#!/bin/bash
#
# Generate ACF stubs of functions only.
#

PLUGIN_VERSION="5.8.0"

Fix_phpdoc()
{
    # - Fix type and variable name order for @param
    # - Remove remaining parentheses for @param
    # - Fix type and variable name order for @return
    # - Remove remaining parentheses for @return
    # - Fix "void"
    find ./includes/ -type f -name "*.php" -exec sed \
        -e 's#^\(\s*\*\s*@param\s\+\)\(\$\S\+\)\s\+(\(\S\+\))\(.*\)$#\1\3 \2\4#' \
        -e 's#^\(\s*\*\s*@param\s\+\)(\(\S\+\))\(.*\)$#\1\2\3#' \
        -e 's#^\(\s*\*\s*@return\s\+\)\(\$\S\+\)\s\+(\(\S\+\))\(.*\)$#\1\3 \2\4#' \
        -e 's#^\(\s*\*\s*@return\s\+\)(\(\S\+\))\(.*\)$#\1\2\3#' \
        -e 's#n/a#void#i' \
        -i "{}" ";"
}

# Check plugin
if ! grep -q 'Plugin Name:\s\+Advanced Custom Fields' ./acf.php 2>/dev/null; then
    echo "Please extract ACF into the current directory!" 1>&2
    echo "wget https://downloads.wordpress.org/plugin/advanced-custom-fields.${PLUGIN_VERSION}.zip && unzip advanced-custom-fields.${PLUGIN_VERSION}.zip" 1>&2
    exit 10
fi

# Generate stubs
if [ ! -x vendor/bin/generate-stubs ]; then
    composer require --no-interaction --update-no-dev --prefer-dist giacocorsiglia/stubs-generator
fi

# Functions only
vendor/bin/generate-stubs --functions --out=acf-stubs-${PLUGIN_VERSION}.php ./includes/

Fix_phpdoc

# Remove determine_locale()
sed -e '/^function determine_locale()/{N;N;d}' -i acf-stubs-${PLUGIN_VERSION}.php
