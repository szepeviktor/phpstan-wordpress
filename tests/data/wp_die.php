<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function PHPStan\Testing\assertType;

/** @var array $array */
$array = null;

$true = ['exit' => true];
$false = ['exit' => false];

assertType('*NEVER*', wp_die('', ''));
assertType('*NEVER*', wp_die('', '', $true));
assertType('void', wp_die('', '', $false));
assertType('void', wp_die('', '', $array));
