<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

// Incorrect usage:
is_wp_error(123);
is_wp_error(wp_insert_post([]));

// Correct usage:
is_wp_error(wp_insert_post([], true));
