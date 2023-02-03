<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use stdClass;

use function get_permalink;
use function get_the_permalink;
use function PHPStan\Testing\assertType;

/** @var \WP_Post $wpPostType */
$wpPostType = new stdClass();

// get_permalink()
assertType('string|false', get_permalink());
assertType('string|false', get_permalink($_GET['foo']));
assertType('string', get_permalink($wpPostType));

// get_the_permalink()
assertType('string|false', get_the_permalink());
assertType('string|false', get_the_permalink($_GET['foo']));
assertType('string', get_the_permalink($wpPostType));
