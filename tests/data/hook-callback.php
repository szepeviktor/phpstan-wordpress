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
add_filter('filter', function() {});

// Filter callback return statement is missing.
add_filter('filter', function() {});

// Filter callback return statement is missing.
add_filter('filter', function(array $classes) {});

// Callback expects 1 parameter, $accepted_args is set to 0.
add_filter('filter', function($value) {
    return 123;
}, 10, 0);

// Callback expects 1 parameter, $accepted_args is set to 2.
add_filter('filter', function($value) {
    return 123;
}, 10, 2);

// Callback expects 0-1 parameters, $accepted_args is set to 2.
add_filter('filter', function($value = null) {
    return 123;
}, 10, 2);

// Callback expects 2 parameters, $accepted_args is set to 1.
add_filter('filter', function($value1, $value2) {
    return 123;
});

// Callback expects 2-4 parameter, $accepted_args is set to 1.
add_filter('filter', function($value1, $value2, $value3 = null, $value4 = null) {
    return 123;
});

// Callback expects 2-3 parameter, $accepted_args is set to 4.
add_filter('filter', function($value1, $value2, $value3 = null) {
    return 123;
}, 10, 4);

// Action callback returns true but should not return anything.
add_action('action', function() {
    return true;
});

// Action callback returns true but should not return anything.
add_action('action', function($value) {
    if ($value) {
        return true;
    }
});

// Filter callback return statement is missing.
add_filter('filter', __NAMESPACE__ . '\\no_return_value_typed');

// Action callback returns false but should not return anything.
add_action('action', '__return_false');

// Action callback returns int but should not return anything.
add_action('action', new TestInvokableTyped(), 10, 2);

// Callback expects at least 1 parameter, $accepted_args is set to 0.
add_filter('filter', __NAMESPACE__ . '\\filter_variadic_typed', 10, 0);

// Callback expects at least 1 parameter, $accepted_args is set to 0.
add_filter('filter', function( $one, ...$two ) {
    return 123;
}, 10, 0);

// Callback expects 0-3 parameters, $accepted_args is set to 4.
add_filter('filter', function($one = null, $two = null, $three = null) {
    return 123;
}, 10, 4);

// Action callback returns int but should not return anything.
add_action('action', __NAMESPACE__ . '\\return_value_typed');

// Callback expects 0 parameters, $accepted_args is set to 2.
add_filter('filter', '__return_false', 10, 2);

// Action callback returns false but should not return anything (with incorrect number of accepted args).
add_action('action', '__return_false', 10, 2);

// Action callback returns mixed but should not return anything.
add_action('action', __NAMESPACE__ . '\\return_value_mixed');

/**
 * Incorrect usage that's handled by PHPStan:
 *
 * These are here to ensure the rule doesn't trigger unwanted errors.
 */

// Too few parameters:
add_filter('filter');
add_filter();

// Invalid callback:
add_filter('filter', 'i_do_not_exist');

// Invalid callback:
add_filter('filter', __NAMESPACE__ . '\\i_do_not_exist');

// Invalid callback:
add_filter('filter', [new TestClassTyped, 'i_do_not_exist']);

// Invalid callback:
add_filter('filter', new TestClassTyped);

// Unknown callback:
add_filter('filter', $_GET['callback']);

// Valid callback but with invalid parameters:
add_filter('filter', function() {
    return 123;
}, false);
add_filter('filter', function() {
    return 123;
}, 10, false);

// Filter callback return statement may be missing.
add_filter('filter', function($class) {
    if ($class) {
        return [];
    }
});

// Incorrect function return type:
add_filter('filter', function(array $classes): array {
    return 123;
});

// Callback function with no declared return type.
add_action('action', __NAMESPACE__ . '\\return_value_untyped');
add_filter('filter', __NAMESPACE__ . '\\no_return_value_untyped');

/**
 * Correct usage:
 */

add_filter('filter', function() {
    return 123;
}, 10, 0);
add_filter('filter', function() {
    // We're allowing 0 parameters when `$accepted_args` is default value of 1.
    // This might change in the future to get more strict.
    return 123;
});
add_filter('filter', function($value) {
    return 123;
});
add_filter('filter', function($value) {
    return 123;
}, 10, 1);
add_filter('filter', function($value1, $value2) {
    return 123;
}, 10, 2);
add_filter('filter', function($value1, $value2, $value3 = null) {
    return 123;
}, 10, 2);
add_filter('filter', function($value1, $value2, $value3 = null) {
    return 123;
}, 10, 3);
add_filter('filter', function($value = null) {
    return 123;
});
add_filter('filter', function($value = null) {
    return 123;
}, 10, 0);
add_filter('filter', function($value = null) {
    return 123;
}, 10, 1);
add_filter('filter', function($one = null, $two = null, $three = null) {
    return 123;
});

// Action callbacks must return void
add_action('action', function() {
    return;
});
add_action('action', function() {
});
add_action('action', function(): void {
});
add_action('action', __NAMESPACE__ . '\\no_return_value_typed');

// Filter callback may exit, unfortunately
add_filter('filter', function(array $classes) {
    if ($classes) {
        exit('Goodbye');
    }

    return $classes;
});

// Action callback may exit, unfortunately
add_action('action', function($result) {
    if (! $result) {
        exit('Goodbye');
    }
});

// Various callback types
add_filter('filter', '__return_false');
add_filter('filter', __NAMESPACE__ . '\\return_value_mixed');
add_filter('filter', __NAMESPACE__ . '\\return_value_untyped');
add_filter('filter', __NAMESPACE__ . '\\return_value_mixed_union');
add_filter('filter', __NAMESPACE__ . '\\return_value_documented');
add_filter('filter', __NAMESPACE__ . '\\return_value_untyped');
add_filter('filter', __NAMESPACE__ . '\\return_value_typed');
add_filter('filter', new TestInvokableTyped(), 10, 2);
add_filter('filter', [new TestClassTyped, 'foo']);

$test_class = new TestClassTyped();

add_filter('filter', [$test_class, 'foo']);

// Variadic callbacks
add_filter('filter', __NAMESPACE__ . '\\filter_variadic_typed');
add_filter('filter', __NAMESPACE__ . '\\filter_variadic_typed', 10, 1);
add_filter('filter', __NAMESPACE__ . '\\filter_variadic_typed', 10, 2);
add_filter('filter', __NAMESPACE__ . '\\filter_variadic_typed', 10, 999);

// Multiple callbacks with varying signatures
class MultipleSignatures {
    const ACTIONS = array(
        'one',
        'two',
    );

    public static function init(): void {
        foreach ( self::ACTIONS as $action ) {
            add_action( 'action', array( self::class, $action ) );
        }
    }

    public static function one( int $param ): void {}

    public static function two( string $param ): void {}
}

/**
 * Symbol definitions for use in these tests.
 */

function no_return_value_typed( $value ) : void {}

function return_value_typed() : int {
    return 123;
}

function no_return_value_untyped( $value ) {}

/**
 * @return mixed
 */
function return_value_mixed() {
    return 123;
}

/**
 * Return type documented as a union that includes mixed.
 *
 * This is added as a regression test for a bug.
 *
 * @return int|mixed
 */
function return_value_mixed_union() {
    return 123;
}

/**
 * @return int
 */
function return_value_documented() {
    return 123;
}

function return_value_untyped() {
    return 123;
}

function filter_variadic_typed( $one, ...$two ) : int {
    return 123;
}

class TestInvokableTyped {
    public function __invoke($one, $two) : int {
        return 123;
    }
}

class TestClassTyped {
    public function foo() : int {
        return 123;
    }
}
