<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function PHPStan\Testing\assertType;

// Default output
assertType('WP_Comment|null', get_comment(1));
assertType('WP_Comment|null', get_comment(1,OBJECT));
assertType('WP_Comment|null', get_comment(1,'Hello'));

// Unknown output
assertType('array|\WP_Comment|null', get_comment(1,_GET['foo']));

// Associative array output
assertType('array<string, mixed>|null', get_comment(1,ARRAY_A));

// Numeric array output
assertType('array<int, mixed>|null', get_comment(1,ARRAY_N));
