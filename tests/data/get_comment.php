<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function PHPStan\Testing\assertType;

/** @var \WP_Comment $comment */
$comment = $comment;

assertType('WP_Comment', get_comment($comment));
assertType('WP_Comment', get_comment($comment, OBJECT));
assertType('array<string, mixed>', get_comment($comment, ARRAY_A));
assertType('array<int, mixed>', get_comment($comment, ARRAY_N));
assertType('array<int|string, mixed>|WP_Comment', get_comment($comment, $_GET['foo']));
