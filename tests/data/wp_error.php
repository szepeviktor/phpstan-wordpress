<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function PHPStan\Testing\assertType;
use function wp_insert_link;

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
