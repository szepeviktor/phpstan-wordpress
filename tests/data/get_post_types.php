<?php

declare( strict_types=1 );

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function PHPStan\Testing\assertType;

assertType('array<int, string>', get_post_types());
assertType('array<int, string>', get_post_types([]));
assertType('array<int, string>', get_post_types([],'names'));
assertType('array<int, WP_Post_Type>', get_post_types([],'objects'));
