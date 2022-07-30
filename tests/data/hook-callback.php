<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function add_filter;
use function add_action;

// phpcs:disable Squiz.NamingConventions.ValidFunctionName.NotCamelCaps,Squiz.NamingConventions.ValidVariableName.NotCamelCaps,Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

/**
 * Incorrect usage:
 */

// Filter callback return statement is missing.
add_filter('not_a_core_filter', function() {});

// Filter callback return statement is missing.
add_filter('post_class', function() {});

// Filter callback return statement is missing.
add_filter('post_class', function(array $classes) {});

// Callback expects 1 parameter, $accepted_args is set to 0.
add_filter('not_a_core_filter', function($value) {
    return 123;
}, 10, 0);

// Callback expects 1 parameter, $accepted_args is set to 2.
add_filter('not_a_core_filter', function($value) {
    return 123;
}, 10, 2);

// Callback expects 0-1 parameters, $accepted_args is set to 2.
add_filter('not_a_core_filter', function($value = null) {
    return 123;
}, 10, 2);

// Callback expects 2 parameters, $accepted_args is set to 1.
add_filter('not_a_core_filter', function($value1, $value2) {
    return 123;
});

// Callback expects 2-4 parameter, $accepted_args is set to 1.
add_filter('not_a_core_filter', function($value1, $value2, $value3 = null, $value4 = null) {
    return 123;
});

// Callback expects 2-3 parameter, $accepted_args is set to 4.
add_filter('not_a_core_filter', function($value1, $value2, $value3 = null) {
    return 123;
}, 10, 4);

// Filter callback return statement is missing.
add_filter('post_class', function($class) {
    if ($class) {
        return [];
    }
});

// Action callback returns true but should not return anything.
add_action('hello', function() {
    return true;
});

// Action callback returns true but should not return anything.
add_action('hello', function($value) {
    if ($value) {
        return true;
    }
});

// Filter callback return statement is missing.
add_filter('not_a_core_filter', __NAMESPACE__ . '\\no_return_value');

// Action callback returns false but should not return anything.
add_action('hello', '__return_false');

// Action callback returns 123 but should not return anything.
add_action('hello', new TestInvokable(), 10, 2);

// Callback expects at least 1 parameter, $accepted_args is set to 0.
add_filter('not_a_core_filter', __NAMESPACE__ . '\\filter_variadic', 10, 0);

// Callback expects at least 1 parameter, $accepted_args is set to 0.
add_filter('not_a_core_filter', function( $one, ...$two ) {
    return 123;
}, 10, 0);

// Callback expects 0-3 parameters, $accepted_args is set to 4.
add_filter('not_a_core_filter', function($one = null, $two = null, $three = null) {
    return 123;
}, 10, 4);

/**
 * Incorrect usage that's handled by PHPStan:
 *
 * These are here to ensure the rule doesn't trigger unwanted errors.
 */

// Too few parameters:
add_filter('post_class');
add_filter();

// Invalid callback:
add_filter('post_class', 'i_do_not_exist');

// Invalid callback:
add_filter('post_class', __NAMESPACE__ . '\\i_do_not_exist');

// Invalid callback:
add_filter('post_class', [new TestClass, 'i_do_not_exist']);

// Invalid callback:
add_filter('post_class', new TestClass);

// Unknown callback:
add_filter('post_class', $_GET['callback']);

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

add_filter('not_a_core_filter', function() {
    return 123;
}, 10, 0);
add_filter('not_a_core_filter', function() {
    // We're allowing 0 parameters when `$accepted_args` is default value of 1.
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
add_filter('not_a_core_filter', function($value1, $value2, $value3 = null) {
    return 123;
}, 10, 2);
add_filter('not_a_core_filter', function($value1, $value2, $value3 = null) {
    return 123;
}, 10, 3);
add_filter('not_a_core_filter', function($value = null) {
    return 123;
});
add_filter('not_a_core_filter', function($value = null) {
    return 123;
}, 10, 0);
add_filter('not_a_core_filter', function($value = null) {
    return 123;
}, 10, 1);
add_filter('not_a_core_filter', function($one = null, $two = null, $three = null) {
    return 123;
});

// Action callbacks must return void
add_action('hello', function() {
    return;
});
add_action('hello', function() {
});
add_action('hello', __NAMESPACE__ . '\\no_return_value');

// Filter callback may exit, unfortunately
add_filter('post_class', function(array $classes) {
    if ($classes) {
        exit('Goodbye');
    }

    return $classes;
});

// Action callback may exit, unfortunately
add_action('hello', function($result) {
    if (! $result) {
        exit('Goodbye');
    }
});

// Various callback types
add_filter('not_a_core_filter', '__return_false');
add_filter('not_a_core_filter', __NAMESPACE__ . '\\return_value');
add_filter('not_a_core_filter', new TestInvokable(), 10, 2);
add_filter('not_a_core_filter', [new TestClass, 'foo']);

$test_class = new TestClass();

add_filter('not_a_core_filter', [$test_class, 'foo']);

// Variadic callbacks
add_filter('not_a_core_filter', __NAMESPACE__ . '\\filter_variadic');
add_filter('not_a_core_filter', __NAMESPACE__ . '\\filter_variadic', 10, 1);
add_filter('not_a_core_filter', __NAMESPACE__ . '\\filter_variadic', 10, 2);
add_filter('not_a_core_filter', __NAMESPACE__ . '\\filter_variadic', 10, 999);

/**
 * Symbol definitions for use in these tests.
 */

function no_return_value( $value ) {}

function return_value() {
    return 123;
}

function filter_variadic( $one, ...$two ) {
    return $one;
}

class TestInvokable {
    public function __invoke($one, $two) {
        return 123;
    }
}

class TestClass {
    public function foo() {
        return 123;
    }
}
