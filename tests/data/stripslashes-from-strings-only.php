<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function stripslashes_from_strings_only;
use function PHPStan\Testing\assertType;

assertType("''", stripslashes_from_strings_only(''));
assertType("'foo'", stripslashes_from_strings_only('foo'));
assertType("'foo\'s bar'", stripslashes_from_strings_only('foo\'s bar'));
/** @var lowercase-string $value */
assertType('lowercase-string', stripslashes_from_strings_only($value));

assertType('array{}', stripslashes_from_strings_only([]));
assertType("array{'foo'}", stripslashes_from_strings_only(['foo']));
assertType("array{'foo\\'s bar'}", stripslashes_from_strings_only(['foo\'s bar']));
