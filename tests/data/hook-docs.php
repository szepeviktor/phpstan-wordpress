<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

$args = apply_filters( 'filter_with_no_docblock', $one, $two );

/**
 * Too few param tags.
 *
 * @param string $one First parameter.
 */
$args = apply_filters( 'filter', $one, $two );

/**
 * Too many param tags.
 *
 * @param string $one   First parameter.
 * @param string $two   Second parameter.
 * @param string $three Third parameter.
 */
$args = apply_filters( 'filter', $one, $two );

/**
 * @param string|int $one
 */
function too_narrow_param_type( $one ) {
    /**
     * This param tag is too narrow.
     *
     * @param string $one First parameter.
     */
    $args = apply_filters( 'filter', $one );
}

function different_param_type( int $one ) {
    /**
     * This param tag does not support int.
     *
     * @param string $one First parameter.
     */
    $args = apply_filters( 'filter', $one );
}

function constant_param_type() {
    /**
     * This param tag accept a constant string, which is fine.
     *
     * @param string $one First parameter.
     */
    $args = apply_filters( 'filter', 'Hello, World' );
}

function wide_param_type( string $one ) {
    /**
     * This param tag is wider than the variable, which is fine.
     *
     * @param string|int $one First parameter.
     */
    $args = apply_filters( 'filter', $one );
}
