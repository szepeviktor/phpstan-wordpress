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

/**
 * This param tag is named `$this`, which is not valid and not supported by PHPStan or PHPDoc.
 *
 * @param stdClass $this Instance.
 * @param array    $args Args
 */
do_action( 'action', $this, $args );

/** This filter is documented in wp-includes/pluggable.php */
$cookie_life = apply_filters( 'auth_cookie_expiration', 172800, get_current_user_id(), false );

/**
 * Docblock with no param tags, which is fine.
 */
$args = apply_filters( 'filter', $one, $two );

function correct_inherited_param_type( \ChildTestClass $one ) {
    /**
     * This param tag is for a super class of the variable, which is fine.
     *
     * @param \ParentTestClass $one First parameter.
     */
    $args = apply_filters( 'filter', $one );
}

function correct_interface_param_type( \ChildTestClass $one ) {
    /**
     * This param tag is for the interface of the variable, which is fine.
     *
     * @param \TestInterface $one First parameter.
     */
    $args = apply_filters( 'filter', $one );
}

function incorrect_inherited_param_type( \ParentTestClass $one ) {
    /**
     * This param tag is for a child class of the variable. Oh no.
     *
     * @param \ChildTestClass $one First parameter.
     */
    $args = apply_filters( 'filter', $one );
}
