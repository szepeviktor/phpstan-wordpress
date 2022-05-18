<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

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
