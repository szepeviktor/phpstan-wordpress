<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function PHPStan\Testing\assertType;

/** @var \WP_Post $wpPostType */
$wpPostType = null;

assertType('WP_Post', get_post($wpPostType));
assertType('WP_Post', get_post($wpPostType, OBJECT));
assertType('array<string, mixed>', get_post($wpPostType, ARRAY_A));
assertType('array<int, mixed>', get_post($wpPostType, ARRAY_N));
assertType('array<int|string, mixed>|WP_Post', get_post($wpPostType, $_GET['foo']));

/** @var \WP_Comment $comment */
$comment = $comment;

assertType('WP_Comment', get_comment($comment));
assertType('WP_Comment', get_comment($comment, OBJECT));
assertType('array<string, mixed>', get_comment($comment, ARRAY_A));
assertType('array<int, mixed>', get_comment($comment, ARRAY_N));
assertType('array<int|string, mixed>|WP_Comment', get_comment($comment, $_GET['foo']));
