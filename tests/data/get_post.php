<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function PHPStan\Testing\assertType;

/** @var \WP_Post $wpPostType */
$wpPostType = new \stdClass();

// Default output
assertType('WP_Post|null', get_post());
assertType('WP_Post|null', get_post(1));
assertType('WP_Post|null', get_post(1, OBJECT));
assertType('array<int|string, mixed>|WP_Post|null', get_post(1, $_GET['foo']));
assertType('WP_Post', get_post($wpPostType));
assertType('WP_Post', get_post($wpPostType, OBJECT));
assertType('array<int|string, mixed>|WP_Post', get_post($wpPostType, $_GET['foo']));
assertType('WP_Post|null', get_page_by_path('page/path'));
assertType('WP_Post|null', get_page_by_path('page/path', OBJECT));
assertType('array<int|string, mixed>|WP_Post|null', get_page_by_path('page/path', $_GET['foo']));

// Associative array output
assertType('array<string, mixed>|null', get_post(null, ARRAY_A));
assertType('array<string, mixed>|null', get_post(1, ARRAY_A));
assertType('array<string, mixed>', get_post($wpPostType, ARRAY_A));
assertType('array<string, mixed>|null', get_page_by_path('page/path', ARRAY_A));

// Numeric array output
assertType('array<int, mixed>|null', get_post(null, ARRAY_N));
assertType('array<int, mixed>|null', get_post(1, ARRAY_N));
assertType('array<int, mixed>', get_post($wpPostType, ARRAY_N));
assertType('array<int, mixed>|null', get_page_by_path('page/path', ARRAY_N));
