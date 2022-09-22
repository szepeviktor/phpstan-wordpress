<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function PHPStan\Testing\assertType;

assertType('array<int, WP_Post>', get_posts());
assertType('array<int, WP_Post>', get_posts(['fields' => 'all']));
assertType('array<int, int>', get_posts(['fields' => 'ids']));
assertType('array<int, int>', get_posts(['fields' => 'id=>parent']));
