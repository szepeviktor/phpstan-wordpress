<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function PHPStan\Testing\assertType;

// Default parameter
assertType('array<int, WP_Site>', get_sites());
assertType('array<int, WP_Site>', get_sites([]));
assertType('array<int, WP_Site>', get_sites(''));

// Non constant array parameter
/** @var array<int|string,mixed> $value */
$value = $_GET['foo'];
assertType('array<int, int|WP_Site>|int', get_sites($value));

// Non constant string parameter
/** @var string $value */
$value = $_GET['foo'];
assertType('array<int, int|WP_Site>|int', get_sites($value));

// Unknown parameter
/** @var mixed $value */
$value = $_GET['foo'];
assertType('array<int, int|WP_Site>|int', get_sites($value));

// Array parameter with explicit fields value
assertType('array<int, int>', get_sites(['fields' => 'ids']));
assertType('array<int, WP_Site>', get_sites(['fields' => '']));
assertType('array<int, WP_Site>', get_sites(['fields' => 'nonEmptyString']));
assertType('array<int, int>', get_sites('fields=ids'));
assertType('array<int, WP_Site>', get_sites('fields='));
assertType('array<int, WP_Site>', get_sites('fields=nonEmptyString'));

// Array parameter with truthy count value.
assertType('int', get_sites(['count' => true]));
assertType('int', get_sites(['count' => 1]));
assertType('int', get_sites(['count' => 'nonEmptyString']));
assertType('int', get_sites(['fields' => '', 'count' => true]));
assertType('int', get_sites(['fields' => '', 'count' => 1]));
assertType('int', get_sites(['fields' => '', 'count' => 'nonEmptyString']));
assertType('int', get_sites(['fields' => 'ids', 'count' => true]));
assertType('int', get_sites(['fields' => 'ids', 'count' => 1]));
assertType('int', get_sites(['fields' => 'ids', 'count' => 'nonEmptyString']));
assertType('int', get_sites(['fields' => 'nonEmptyString', 'count' => true]));
assertType('int', get_sites(['fields' => 'nonEmptyString', 'count' => 1]));
assertType('int', get_sites(['fields' => 'nonEmptyString', 'count' => 'nonEmptyString']));

// String parameter with truthy count value
assertType('int', get_sites('count=true'));
assertType('int', get_sites('count=false'));
assertType('int', get_sites('count=1'));
assertType('int', get_sites('count=nonEmptyString'));
assertType('int', get_sites('fields=&count=true'));
assertType('int', get_sites('fields=&count=false'));
assertType('int', get_sites('fields=&count=1'));
assertType('int', get_sites('fields=&count=nonEmptyString'));
assertType('int', get_sites('fields=ids&count=true'));
assertType('int', get_sites('fields=ids&count=false'));
assertType('int', get_sites('fields=ids&count=1'));
assertType('int', get_sites('fields=ids&count=nonEmptyString'));
assertType('int', get_sites('fields=ids&count=true'));
assertType('int', get_sites('fields=nonEmptyString&count=false'));
assertType('int', get_sites('fields=nonEmptyString&count=1'));
assertType('int', get_sites('fields=nonEmptyString&count=nonEmptyString'));

// Array parameter with falsy count value
assertType('array<int, WP_Site>', get_sites(['count' => false]));
assertType('array<int, WP_Site>', get_sites(['count' => 0]));
assertType('array<int, WP_Site>', get_sites(['count' => '']));
assertType('array<int, WP_Site>', get_sites(['fields' => '', 'count' => false]));
assertType('array<int, WP_Site>', get_sites(['fields' => '', 'count' => 0]));
assertType('array<int, WP_Site>', get_sites(['fields' => '', 'count' => '']));
assertType('array<int, int>', get_sites(['fields' => 'ids', 'count' => false]));
assertType('array<int, int>', get_sites(['fields' => 'ids', 'count' => 0]));
assertType('array<int, int>', get_sites(['fields' => 'ids', 'count' => '']));
assertType('array<int, WP_Site>', get_sites(['fields' => 'nonEmptyString', 'count' => false]));
assertType('array<int, WP_Site>', get_sites(['fields' => 'nonEmptyString', 'count' => 0]));
assertType('array<int, WP_Site>', get_sites(['fields' => 'nonEmptyString', 'count' => '']));

// String parameter with falsy count value
assertType('array<int, WP_Site>', get_sites('count=0'));
assertType('array<int, WP_Site>', get_sites('count='));
assertType('array<int, WP_Site>', get_sites('fields=&count=0'));
assertType('array<int, WP_Site>', get_sites('fields=&count='));
assertType('array<int, int>', get_sites('fields=ids&count=0'));
assertType('array<int, int>', get_sites('fields=ids&count='));
assertType('array<int, WP_Site>', get_sites('fields=nonEmptyString&count=0'));
assertType('array<int, WP_Site>', get_sites('fields=nonEmptyString&count='));
