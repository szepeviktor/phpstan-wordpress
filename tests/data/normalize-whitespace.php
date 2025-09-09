<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function normalize_whitespace;
use function PHPStan\Testing\assertType;

assertType("''", normalize_whitespace(''));
assertType("''", normalize_whitespace(' '));
assertType("'0'", normalize_whitespace(' 0 '));

assertType('literal-string&lowercase-string&non-falsy-string', normalize_whitespace(' foo '));
assertType('literal-string&non-falsy-string', normalize_whitespace(' Foo '));

/** @var non-empty-string $nonEmptyString */
assertType('string', normalize_whitespace($nonEmptyString));

/** @var non-falsy-string $nonFalsyString */
assertType('non-empty-string', normalize_whitespace($nonFalsyString));

/** @var lowercase-string&non-falsy-string $nonFalsyLowercaseString */
assertType('lowercase-string&non-empty-string', normalize_whitespace($nonFalsyLowercaseString));

/** @var literal-string $literalString */
assertType('literal-string', normalize_whitespace($literalString));
