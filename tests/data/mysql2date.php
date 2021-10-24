<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function PHPStan\Testing\assertType;

// Constant strings
assertType('int|false', mysql2date('G'));
assertType('int|false', mysql2date('U'));
assertType('string|false', mysql2date('Y-m-d H:i:s'));

// Unknown types
assertType('mixed', $_GET['foo']);
assertType('int|string|false', mysql2date($_GET['foo']));
assertType('int|string|false', mysql2date(get_option('date_format')));

// Unsupported types
assertType('int|string|false', mysql2date(new \stdClass));
assertType('int|string|false', mysql2date(false));
