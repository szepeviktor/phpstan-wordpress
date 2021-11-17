<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use stdClass;

use function PHPStan\Testing\assertType;

$time = '1970-01-01 00:00:00';

// Constant strings
assertType('int|false', mysql2date('G', $time));
assertType('int|false', mysql2date('U', $time));
assertType('string|false', mysql2date('Y-m-d H:i:s', $time));

// Unknown types
assertType('mixed', $_GET['foo']);
assertType('int|string|false', mysql2date($_GET['foo'], $time));
assertType('int|string|false', mysql2date(get_option('date_format'), $time));

// Unsupported types
assertType('int|string|false', mysql2date(new stdClass(), $time));
assertType('int|string|false', mysql2date(false, $time));
