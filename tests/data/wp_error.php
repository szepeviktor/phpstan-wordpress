<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function PHPStan\Testing\assertType;
use function wp_insert_category;
use function wp_insert_link;
use function wp_set_comment_status;

/**
 * wp_insert_link()
 */
$value = wp_insert_link([]);
assertType('int', $value);

$value = wp_insert_link([],false);
assertType('int', $value);

$value = wp_insert_link([],true);
assertType('int|WP_Error', $value);

$value = wp_insert_link([],$_GET['wp_error']);
assertType('int|WP_Error', $value);

/**
 * wp_insert_category()
 */
$value = wp_insert_category([]);
assertType('int', $value);

$value = wp_insert_category([],false);
assertType('int', $value);

$value = wp_insert_category([],true);
assertType('int|WP_Error', $value);

$value = wp_insert_category([],$_GET['wp_error']);
assertType('int|WP_Error', $value);

/**
 * wp_set_comment_status()
 */
$value = wp_set_comment_status(1,'spam');
assertType('bool', $value);

$value = wp_set_comment_status(1,'spam',false);
assertType('bool', $value);

$value = wp_set_comment_status(1,'spam',true);
assertType('WP_Error|true', $value);

$value = wp_set_comment_status(1,'spam',$_GET['wp_error']);
assertType('bool|WP_Error', $value);
