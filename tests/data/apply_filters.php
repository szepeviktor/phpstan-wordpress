<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function apply_filters;
use function PHPStan\Testing\assertType;

/**
 * Returns the passed value.
 *
 * @template T
 * @param T $value Value.
 * @return T Value.
 */
function return_value( $value ) {
    return $value;
}

$value = apply_filters('filter','Hello, World');
assertType('mixed', $value);

/**
 * Unknown parameter.
 */
$value = apply_filters('filter',$foo);
assertType('mixed', $value);

/** @var int $value */
$value = apply_filters('filter',$foo);
assertType('int', $value);

/**
 * Single type.
 *
 * @param string $foo Hello, World.
 */
$value = apply_filters('filter',$foo);
assertType('string', $value);

/**
 * Single constant type.
 *
 * @param string $foo Hello, World.
 */
$value = apply_filters('filter','I am a string');
assertType('string', $value);

/**
 * Single constant of a type that differs from the docblock.
 *
 * @TODO Need to decide whether this should result in a type of `string|int` or whether we can
 * get PHPStan to trigger a warning in this situation.
 *
 * @param string $foo Hello, World.
 */
$value = apply_filters('filter',123);
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

/**
 * Value assignment wrapped inside another function.
 *
 * @param string $foo Hello, World.
 */
$value = return_value(apply_filters('filter',$foo));
assertType('string', $value);

/**
 * Filters the maximum image size dimensions for the editor.
 *
 * @since 2.5.0
 *
 * @param int[]        $max_image_size {
 *     An array of width and height values.
 *
 *     @type int $0 The maximum width in pixels.
 *     @type int $1 The maximum height in pixels.
 * }
 * @param string|int[] $size     Requested image size. Can be any registered image size name, or
 *                               an array of width and height values in pixels (in that order).
 * @param string       $context  The context the image is being resized for.
 *                               Possible values are 'display' (like in a theme)
 *                               or 'edit' (like inserting into an editor).
 */
list($max_width, $max_height) = apply_filters('editor_max_image_size', [$max_width, $max_height], $size, $context);
assertType('int', $max_width);
assertType('int', $max_height);
