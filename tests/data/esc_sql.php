<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use stdClass;

use function PHPStan\Testing\assertType;

// Indexed array as parameter
assertType('array<int, string>', esc_sql(['someValue', 'toEscape']));

// Associative array as parameter
assertType('array<string, string>', esc_sql(['someValue' => 'toEscape']));

// String as parameter
assertType('string', esc_sql('someValueToEscape'));

// Wrong type provided (esc_sql() returns an empty string in that case)
assertType('string', esc_sql(new \stdClass()));
