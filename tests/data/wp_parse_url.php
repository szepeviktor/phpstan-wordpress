<?php

/**
 * Test data for WpParseUrlFunctionDynamicReturnTypeExtension.
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function PHPStan\Testing\assertType;
use function wp_clear_scheduled_hook;
use function wp_insert_attachment;
use function wp_insert_category;
use function wp_insert_link;
use function wp_insert_post;
use function wp_reschedule_event;
use function wp_schedule_event;
use function wp_schedule_single_event;
use function wp_set_comment_status;
use function wp_unschedule_event;
use function wp_unschedule_hook;
use function wp_update_comment;
use function wp_update_post;

/** @var int $integer */
$integer = doFoo();

/** @var string $string */
$string = doFoo();

/**
 * wp_parse_url()
 */
$value = wp_parse_url();
assertType('mixed', $value);

$value = wp_parse_url('http://abc.def');
assertType("array{scheme: 'http', host: 'abc.def'}", $value);

$value = wp_parse_url('http://def.abc', -1);
assertType("array{scheme: 'http', host: 'def.abc'}", $value);

$value = wp_parse_url('http://def.abc', $integer);
assertType('array{scheme?: string, host?: string, port?: int, user?: string, pass?: string, path?: string, query?: string, fragment?: string}|false', $value);

$value = wp_parse_url('http://def.abc', PHP_URL_FRAGMENT);
assertType('null', $value);

$value = wp_parse_url('http://def.abc#this-is-fragment', PHP_URL_FRAGMENT);
assertType("'this-is-fragment'", $value);

$value = wp_parse_url('http://def.abc#this-is-fragment', 9999);
assertType('false', $value);

$value = wp_parse_url($string, 9999);
assertType('false', $value);

$value = wp_parse_url($string, PHP_URL_PORT);
assertType('int|false|null', $value);

$value = wp_parse_url($string);
assertType('array{scheme?: string, host?: string, port?: int, user?: string, pass?: string, path?: string, query?: string, fragment?: string}|false', $value);
