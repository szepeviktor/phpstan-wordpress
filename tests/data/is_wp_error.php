<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

/** @var mixed $mixed */
$mixed = $mixed;

/** @var \WP_Error|int $yes */
$yes = $yes;

/** @var int|false $no */
$no = $no;

// Incorrect usage:
is_wp_error(123);
is_wp_error($no);

// Correct usage:
is_wp_error($mixed);
is_wp_error($yes);
