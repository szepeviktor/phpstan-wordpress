<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use stdClass;

use function current_time;
use function PHPStan\Testing\assertType;

// Integer types
// phpcs:disable WordPress.DateTime.CurrentTimeTimestamp.Requested
assertType('int', current_time('timestamp'));
assertType('int', current_time('U'));
// phpcs:enable

// String types
assertType('string', current_time('mysql'));
assertType('string', current_time('Hello'));

// Unknown types
assertType('int|string', current_time($_GET['foo']));
assertType('int|string', current_time(get_option('date_format')));

// Unsupported types
assertType('int|string', current_time(new stdClass()));
assertType('int|string', current_time(false));
