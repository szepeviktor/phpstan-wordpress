<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function add_filter;
use function add_action;

// phpcs:disable Squiz.NamingConventions.ValidFunctionName.NotCamelCaps,Squiz.NamingConventions.ValidVariableName.NotCamelCaps,Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

/*
 * Incorrect usage:
 */

// Filter callback return statement is missing.
add_filter(callback: static function () {}, hook_name: 'filter');
add_filter(callback: static function (array $classes) {}, hook_name: 'filter');

// Callback expects 1 parameter, $accepted_args is set to 0.
add_filter(callback: static function ($value): int {
    return 123;
}, priority: 10, accepted_args: 0, hook_name: 'filter');

// Callback expects 1 parameter, $accepted_args is set to 2.
add_filter(callback: static function ($value): int {
    return 123;
}, accepted_args: 2, priority: 10, hook_name: 'filter');

// Callback expects 0-1 parameters, $accepted_args is set to 2.
add_filter(callback: static function ($value = null): int {
    return 123;
}, priority: 10, accepted_args: 2, hook_name: 'filter');

// Callback expects 2 parameters, $accepted_args is set to 1.
add_filter(callback: static function ($value1, $value2): int {
    return 123;
}, hook_name: 'filter');

// Callback expects 2-4 parameter, $accepted_args is set to 1.
add_filter(callback: static function ($value1, $value2, $value3 = null, $value4 = null): int {
    return 123;
}, hook_name: 'filter');

// Callback expects 2-3 parameter, $accepted_args is set to 4.
add_filter(callback: static function ($value1, $value2, $value3 = null): int {
    return 123;
}, priority: 10, accepted_args: 4, hook_name: 'filter');

// Action callback returns true but should not return anything.
add_action(callback: static function () {
    return true;
}, hook_name: 'action');
add_action(callback: static function ($value) {
    if ($value) {
        return true;
    }
}, hook_name: 'action');

// Filter callback return statement is missing.
add_filter(callback: __NAMESPACE__ . '\\no_return_value_typed', hook_name: 'filter');

// Action callback returns false but should not return anything.
add_action(callback: '__return_false', hook_name: 'action');

// Action callback returns int but should not return anything.
add_action(callback: new TestInvokableTyped(), priority: 10, accepted_args: 2, hook_name: 'action');

// Callback expects at least 1 parameter, $accepted_args is set to 0.
add_filter(callback: __NAMESPACE__ . '\\filter_variadic_typed', priority: 10, accepted_args: 0, hook_name: 'filter');

// Callback expects at least 1 parameter, $accepted_args is set to 0.
add_filter(callback: static function ($one, ...$two): int {
    return 123;
}, priority: 10, accepted_args: 0, hook_name: 'filter');

// Callback expects 0-3 parameters, $accepted_args is set to 4.
add_filter(callback: static function ($one = null, $two = null, $three = null): int {
    return 123;
}, priority: 10, accepted_args: 4, hook_name: 'filter');

// Action callback returns int but should not return anything.
add_action(callback: __NAMESPACE__ . '\\return_value_typed', hook_name: 'action');

// Callback expects 0 parameters, $accepted_args is set to 2.
add_filter(callback: '__return_false', priority: 10, accepted_args: 2, hook_name: 'filter');

// Action callback returns false but should not return anything (with incorrect number of accepted args).
add_action(callback: '__return_false', priority: 10, accepted_args: 2, hook_name: 'action');

// Action callback returns mixed but should not return anything.
add_action(callback: __NAMESPACE__ . '\\return_value_mixed', hook_name: 'action');

// Action callback returns null but should not return anything.
add_action(callback: '__return_null', hook_name: 'action');

/*
 * Incorrect usage that's handled by PHPStan:
 *
 * These are here to ensure the rule doesn't trigger unwanted errors.
 */

// Too few parameters:
add_filter(hook_name: 'filter');
add_filter();

// Invalid callback:
add_filter(callback: 'i_do_not_exist', hook_name: 'filter');
add_filter(callback: __NAMESPACE__ . '\\i_do_not_exist', hook_name: 'filter');
add_filter(callback: [new TestClassTyped, 'i_do_not_exist'], hook_name: 'filter');
add_filter(callback: new TestClassTyped, hook_name: 'filter');
add_filter(callback: $_GET['callback'], hook_name: 'filter');

// Valid callback but with invalid parameters:
add_filter(callback: static function () {
    return 123;
}, priority: false, hook_name: 'filter');
add_filter(callback: static function () {
    return 123;
}, priority: 10, accepted_args: false, hook_name: 'filter');

// Filter callback return statement may be missing.
add_filter(callback: static function ($class) {
    if ($class) {
        return [];
    }
}, hook_name: 'filter');

// Incorrect function return type:
add_filter(callback: static function (array $classes): array {
    return 123;
}, hook_name: 'filter');

// Callback function with no declared return type.
add_action(callback: __NAMESPACE__ . '\\return_value_untyped', hook_name: 'action');
add_filter(callback: __NAMESPACE__ . '\\no_return_value_untyped', hook_name: 'filter');

/*
 * Correct usage:
 */

add_filter(callback: static function () {
    return 123;
}, priority: 10, accepted_args: 0, hook_name: 'filter');
add_filter(callback: static function () {
    // We're allowing 0 parameters when `$accepted_args` is default value of 1.
    // This might change in the future to get more strict.
    return 123;
}, hook_name: 'filter');
add_filter(callback: static function ($value) {
    return 123;
}, hook_name: 'filter');
add_filter(callback: static function ($value) {
    return 123;
}, priority: 10, accepted_args: 1, hook_name: 'filter');
add_filter(callback: static function ($value1, $value2) {
    return 123;
}, priority: 10, accepted_args: 2, hook_name: 'filter');
add_filter(callback: static function ($value1, $value2, $value3 = null) {
    return 123;
}, priority: 10, accepted_args: 2, hook_name: 'filter');
add_filter(callback: static function ($value1, $value2, $value3 = null) {
    return 123;
}, priority: 10, accepted_args: 3, hook_name: 'filter');
add_filter(callback: static function ($value = null) {
    return 123;
}, hook_name: 'filter');
add_filter(callback: static function ($value = null) {
    return 123;
}, priority: 10, accepted_args: 0, hook_name: 'filter');
add_filter(callback: static function ($value = null) {
    return 123;
}, priority: 10, accepted_args: 1, hook_name: 'filter');
add_filter(callback: static function ($one = null, $two = null, $three = null) {
    return 123;
}, hook_name: 'filter');

// Action callbacks must return void
add_action(callback: static function () {
    return;
}, hook_name: 'action');
add_action(callback: static function () {}, hook_name: 'action');
add_action(callback: static function (): void {}, hook_name: 'action');
add_action(callback: __NAMESPACE__ . '\\no_return_value_typed', hook_name: 'action');

// Filter callback may exit, unfortunately
add_filter(callback: static function (array $classes) {
    if ($classes) {
        exit('Goodbye');
    }

    return $classes;
}, hook_name: 'filter');

// Action callback may exit, unfortunately
add_action(callback: static function ($result) {
    if (! $result) {
        exit('Goodbye');
    }
}, hook_name: 'action');

// Various callback types
add_filter(callback: '__return_true', hook_name: 'filter');
add_filter(callback: '__return_false', hook_name: 'filter');
add_filter(callback: '__return_zero', hook_name: 'filter');
add_filter(callback: '__return_null', hook_name: 'filter');
add_filter(callback: '__return_empty_array', hook_name: 'filter');
add_filter(callback: '__return_empty_string', hook_name: 'filter');
add_filter(callback: __NAMESPACE__ . '\\return_value_mixed', hook_name: 'filter');
add_filter(callback: __NAMESPACE__ . '\\return_value_mixed_union', hook_name: 'filter');
add_filter(callback: __NAMESPACE__ . '\\return_value_documented', hook_name: 'filter');
add_filter(callback: __NAMESPACE__ . '\\return_value_untyped', hook_name: 'filter');
add_filter(callback: __NAMESPACE__ . '\\return_value_typed', hook_name: 'filter');
add_filter(callback: new TestInvokableTyped(), priority: 10, accepted_args: 2, hook_name: 'filter');
add_filter(callback: [new TestClassTyped, 'foo'], hook_name: 'filter');

$test_class = new TestClassTyped();

add_filter(callback: [$test_class, 'foo'], hook_name: 'filter');

// Variadic callbacks
add_filter(callback: __NAMESPACE__ . '\\filter_variadic_typed', hook_name: 'filter');
add_filter(callback: __NAMESPACE__ . '\\filter_variadic_typed', priority: 10, accepted_args: 1, hook_name: 'filter');
add_filter(callback: __NAMESPACE__ . '\\filter_variadic_typed', priority: 10, accepted_args: 2, hook_name: 'filter');
add_filter(callback: __NAMESPACE__ . '\\filter_variadic_typed', priority: 10, accepted_args: 999, hook_name: 'filter');

// Mixed named und positional arguments
add_filter('filter', priority: 2, callback: static function (): int {
    return 123;
});
add_filter('filter', priority: 2, callback: static function ($value): int {
    return 123;
});
add_filter('filter', accepted_args: 2, priority: 2, callback: static function ($one, $two): int {
    return 123;
});

// Multiple callbacks with varying signatures
class MultipleSignatures {
    const ACTIONS = [
        'one',
        'two',
    ];

    public static function init(): void {
        foreach (self::ACTIONS as $action) {
            add_action(callback: [self::class, $action], hook_name: 'action');
        }
    }

    public static function one(int $param): void {}

    public static function two(string $param): void {}
}

/*
 * Symbol definitions for use in these tests.
 */

function no_return_value_typed($value): void {}

function return_value_typed(): int {
    return 123;
}

function no_return_value_untyped($value) {}

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

function filter_variadic_typed($one, ...$two): int {
    return 123;
}

class TestInvokableTyped {
    public function __invoke($one, $two): int {
        return 123;
    }
}

class TestClassTyped {
    public function foo(): int {
        return 123;
    }
}
