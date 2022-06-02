<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function add_filter;
use function add_action;

// phpcs:disable Squiz.NamingConventions.ValidFunctionName.NotCamelCaps,Squiz.NamingConventions.ValidVariableName.NotCamelCaps,Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

/**
 * Incorrect usage:
 */

// Filter callback is missing a return value
add_filter('not_a_core_filter', function() {});

// Filter callback is missing a return value
add_filter('post_class', function() {});
add_filter('post_class', function(array $classes) {});

// Accepted args are incorrect
add_filter('not_a_core_filter', function($value) {
    return 123;
}, 10, 0);
add_filter('not_a_core_filter', function($value) {
    return 123;
}, 10, 2);
add_filter('not_a_core_filter', function($value1, $value2) {
    return 123;
});

// Filter callback may miss a return value
add_filter('post_class', function($class) {
    if ($class) {
        return [];
    }
});

// Action callback must return void
add_action('hello', function() {
    return true;
});
add_action('hello', function($value) {
    if ($value) {
        return true;
    }
});

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

// Unknown callback:
add_filter('post_class',$_GET['callback']);

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

// But callback is ok
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

// Action callbacks must return void
add_action('hello', function() {
    return;
});
add_action('hello', function() {
});

// Filter callback may exit, unfortunately
add_filter('post_class', function(array $classes) {
    if ($classes) {
        exit('Goodbye');
    }

    return $classes;
});

// Various callback types
add_filter('not_a_core_filter', '__return_false');
add_filter('not_a_core_filter', __NAMESPACE__ . '\\filter_callback');
add_filter('not_a_core_filter', new TestInvokable(), 10, 2);

function filter_callback() {
    return 123;
}

class TestInvokable {
	public function __invoke($one, $two) {
        return 123;
	}
}
