<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function apply_filters;
use function PHPStan\Testing\assertType;
use stdClass;

$value = apply_filters('filter','Hello, World');
assertType('mixed', $value);

/**
 * Single type.
 *
 * @param string $foo Hello, World.
 */
$value = apply_filters('filter',$foo);
assertType('string', $value);

/**
 * Union type.
 *
 * @param string|null $foo Hello, World.
 */
$value = apply_filters('filter',$foo);
assertType('string|null', $value);

/**
 * WordPress array hash notation.
 *
 * @param array $foo {
 *     Hello, World.
 *
 *     @type string $bar Bar.
 * }
 */
$value = apply_filters('filter',$foo);
assertType('array', $value);
