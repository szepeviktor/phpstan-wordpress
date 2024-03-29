<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function PHPStan\Testing\assertType;

/** @var array $array */
$array = null;

assertType('never', wp_die());
assertType('never', wp_die(''));
assertType('never', wp_die('', ''));
assertType('never', wp_die('', '', ['exit' => true]));
assertType('void', wp_die('', '', ['exit' => false]));
assertType('void', wp_die('', '', $array));
