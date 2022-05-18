<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function add_filter;
use function add_action;

// phpcs:disable Squiz.NamingConventions.ValidFunctionName.NotCamelCaps,Squiz.NamingConventions.ValidVariableName.NotCamelCaps,Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

/**
 * Incorrect usage:
 */

// Not a core filter, but callback is missing a return value
add_filter('not_a_core_filter', function() {});

// Core filter, callback is missing a return value
add_filter('post_class', function() {});
add_filter('post_class', function(array $classes) {});

// Not a core filter, accepted args are incorrect
add_filter('not_a_core_filter', function($value) {
    return 123;
}, 10, 0);
add_filter('not_a_core_filter', function($value) {
    return 123;
}, 10, 2);
add_filter('not_a_core_filter', function($value1, $value2) {
    return 123;
});

// Core filter, callback is missing a return value
add_filter('post_class', function() {});
add_filter('post_class', function(array $classes) {});

/**
 * Incorrect usage that's handled by PHPStan:
 *
 * These are here to ensure the rule doesn't trigger unwanted errors.
 */

// Too few parameters:
add_filter('post_class');
add_filter();

// Invalid callback:
add_filter('post_class','i_do_not_exist');

// Invalid parameters:
add_filter('post_class', function() {
    return 123;
}, false);
add_filter('post_class', function() {
    return 123;
}, 10, false);

/**
 * Correct usage:
 */

// Not a core filter, but callback is ok
add_filter('not_a_core_filter', function() {
    return 123;
}, 10, 0);
add_filter('not_a_core_filter', function() {
    // We're allowing 0 parameters when `$accepted_args` is default value.
    // This might change in the future to get more strict.
    return 123;
});
add_filter('not_a_core_filter', function($value) {
    return 123;
});
add_filter('not_a_core_filter', function($value) {
    return 123;
}, 10, 1);
add_filter('not_a_core_filter', function($value1, $value2) {
    return 123;
}, 10, 2);
add_filter('not_a_core_filter', '__return_false');
