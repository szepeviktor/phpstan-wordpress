<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function apply_filters;
use function PHPStan\Testing\assertType;
use WP_Post;

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
 * Multiple lines and multiple parameters.
 *
 * @param string $foo Hello.
 * @param bool   $bar World.
 */
$value = apply_filters(
    'filter',
    $foo,
    $bar
);
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

$value = return_value(
    return_value(
        return_value(
            /**
             * Multiple nested function calls.
             *
             * @param string $foo Hello, World.
             */
            apply_filters('filter',$foo)
        )
    )
);
assertType('string', $value);

/**
 * Incorrect docblock placement that should be ignored.
 *
 * @param string $foo Hello, World.
 */
$value = return_value(
    return_value(
        return_value(
            apply_filters('filter',$foo)
        )
    )
);
assertType('mixed', $value);

/**
 * Casting to a type.
 *
 * @param float|int|null $foo Hello, World.
 */
$value = (int) apply_filters('filter',$foo);
assertType('int', $value);

/**
 * Global class that's been imported.
 *
 * @param WP_Post|null $foo Hello, World.
 */
$value = apply_filters('filter',$foo);
assertType('WP_Post|null', $value);

/**
 * Global class that's not been imported.
 *
 * @param \WP_Term|null $foo Hello, World.
 */
$value = apply_filters('filter',$foo);
assertType('WP_Term|null', $value);

/**
 * Constant string type.
 *
 * @param 'aaa'|'bbb' $foo Hello, World.
 */
$value = apply_filters('filter',$foo);
assertType("'aaa'|'bbb'", $value);

/**
 * Typed array passed through `list()`.
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

/**
 * Filter inside a ternary.
 *
 * @param string          $slug The editable slug. Will be either a term slug or post URI depending
 *                              upon the context in which it is evaluated.
 * @param WP_Term|WP_Post $tag  Term or post object.
 */
$slug = isset($tag->slug) ? apply_filters('editable_slug', $tag->slug, $tag) : 123;
assertType('123|string', $slug);

/** This filter is documented in foo.php */
$value = apply_filters('foo', 123);
assertType('mixed', $value);
