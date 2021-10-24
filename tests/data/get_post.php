<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function PHPStan\Testing\assertType;

// Default output
assertType('WP_Post|null', get_post(1));
assertType('WP_Post|null', get_post(1,OBJECT));
assertType('WP_Post|null', get_post(1,'Hello'));

// Unknown output
assertType('array|\WP_Post|null', get_post(1,_GET['foo']));

// Associative array output
assertType('array<string, mixed>|null', get_post(1,ARRAY_A));

// Numeric array output
assertType('array<int, mixed>|null', get_post(1,ARRAY_N));
