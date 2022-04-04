<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

/** @var mixed $mixed */
$mixed = $mixed;

/** @var \WP_Error|int $yes */
$yes = $yes;

/** @var int|false $no */
$no = $no;

/** @var \WP_Post $post */
$post = $post;

/** @var \WP_Error $always */
$always = $always;

// Incorrect usage:
is_wp_error(123);
is_wp_error($no);
is_wp_error($always);
is_wp_error($post);

// Correct usage:
is_wp_error($mixed);
is_wp_error($yes);
