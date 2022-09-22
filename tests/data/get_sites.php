<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function PHPStan\Testing\assertType;

assertType('array<int, WP_Site>', get_sites());
assertType('int', get_sites(['fields' => 'count']));
assertType('array<int, int>', get_sites(['fields' => 'ids']));
