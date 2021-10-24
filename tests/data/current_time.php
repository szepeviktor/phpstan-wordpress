<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function current_time;
use function PHPStan\Testing\assertType;
use stdClass;

// Integer types
assertType('int', current_time('timestamp'));
assertType('int', current_time('U'));

// String types
assertType('string', current_time('mysql'));
assertType('string', current_time('Hello'));

// Unknown types
assertType('int|string', current_time($_GET['foo']));
assertType('int|string', current_time(get_option('date_format')));

// Unsupported types
assertType('int|string', current_time(new stdClass));
assertType('int|string', current_time(false));
