<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

// phpcs:disable Squiz.NamingConventions.ValidFunctionName.NotCamelCaps,Squiz.NamingConventions.ValidVariableName.NotCamelCaps,Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

$one = 1;
$two = 2;

$args = apply_filters('filter_with_no_docblock', $one, $two);

/**
 * Too few param tags.
 *
 * @param string $one First parameter.
 */
$args = apply_filters('filter', $one, $two);

/**
 * Too many param tags.
 *
 * @param string $one   First parameter.
 * @param string $two   Second parameter.
 * @param string $three Third parameter.
 */
$args = apply_filters('filter', $one, $two);

/**
 * @param string|int $one
 */
function too_narrow_param_type($one)
{
    /**
     * This param tag is too narrow.
     *
     * @param string $one First parameter.
     */
    $args = apply_filters('filter', $one);
}

function different_param_type(int $one)
{
    /**
     * This param tag does not support int.
     *
     * @param string $one First parameter.
     */
    $args = apply_filters('filter', $one);
}

function constant_param_type()
{
    /**
     * This param tag accept a constant string, which is fine.
     *
     * @param string $one First parameter.
     */
    $args = apply_filters('filter', 'Hello, World');
}

function wide_param_type(string $one)
{
    /**
     * This param tag is wider than the variable, which is fine.
     *
     * @param string|int $one First parameter.
     */
    $args = apply_filters('filter', $one);
}

/**
 * This param tag is named `$this`, which is not valid and not supported by PHPStan or PHPDoc.
 *
 * @param stdClass $this Instance.
 * @param array    $args Args
 */
do_action('action', $this, $args);

/**
 * This param tag renames the `$this` variable, which is fine.
 *
 * @param \stdClass $instance Instance.
 * @param array    $args     Args
 */
do_action('action', $this, []);

/**
 * This param tag renames the `$this` variable, which is fine, but still has a missing param tag.
 *
 * @param \stdClass $instance Instance.
 */
do_action('action', $this, $args);

/** This filter is documented in wp-includes/pluggable.php */
$cookie_life = apply_filters('auth_cookie_expiration', 172800, get_current_user_id(), false);

/**
 * Docblock with no param tags, which is fine.
 */
$args = apply_filters('filter', $one, $two);

function correct_inherited_param_type(ChildTestClass $one)
{
    /**
     * This param tag is for a super class of the variable, which is fine.
     *
     * @param ParentTestClass $one First parameter.
     */
    $args = apply_filters('filter', $one);
}

function correct_interface_param_type(ChildTestClass $one)
{
    /**
     * This param tag is for the interface of the variable, which is fine.
     *
     * @param TestInterface $one First parameter.
     */
    $args = apply_filters('filter', $one);
}

function incorrect_inherited_param_type(ParentTestClass $one)
{
    /**
     * This param tag is for a child class of the variable. Oh no.
     *
     * @param ChildTestClass $one First parameter.
     */
    $args = apply_filters('filter', $one);
}

function correct_nullable_param_types(?string $one = null, ?int $two = null)
{
    /**
     * These param tags should accept nullables.
     *
     * @param string|null $one First parameter.
     * @param ?int        $two Second parameter.
     */
    $args = apply_filters('filter', $one, $two);
}

function incorrect_nullable_param_type(?string $one = null)
{
    /**
     * This param tag should accept string or null.
     *
     * @param string $one First parameter.
     */
    $args = apply_filters('filter', $one);
}

/**
 * Variable functions can cause parse errors.
 */
$my_function();

/**
 * The first docblock contains an invalid variable name.
 *
 * @param bool false Should Photon ignore this image. Default to false.
 * @param string $src Image URL.
 * @param string $tag Image Tag (Image HTML output).
 */
if (apply_filters('filter', false, $src, $tag)) {
    return;
}

/**
 * This docblock mentions @param in it but doesn't actually contain one. This is fine.
 */
$args = apply_filters('filter', $one);

/**
 * These param tags are missing descriptions but otherwise valid, which is fine.
 *
 * @param bool $false
 * @param string $src
 * @param string $tag
 */
if (apply_filters('filter', false, $src, $tag)) {
    return;
}

/**
 * Nested @type tags are ok.
 *
 * @param array $args {
 *     @type array $instance The widget instance.
 *     @type object $object The class object.
 * }
 */
do_action('filter', [$instance, $this]);

/**
 * Can't use a function name as a param name.
 *
 * @param bool has_site_pending_automated_transfer( $this->blog_id )
 * @param int  $blog_id Blog identifier.
 */
$value = apply_filters(
    'filter',
    false,
    $this->blog_id
);

/**
 * This filter is wrapped inside another function call, which is weird but ok. Its param count is incorrect.
 *
 * @param int $number
 */
$value = intval(apply_filters('filter', 123, $foo));

/**
 * This is a docblock for an unrelated function.
 *
 * It exists to ensure the undocumented filter below does not have its docblock inherited from this function.
 *
 * @param bool $yes
 */
function foo( bool $yes ) {}

$value = apply_filters('filter', 123, $foo);
