<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Returns the passed value.
 *
 * @template T
 * @param T $value Value.
 * @return T Value.
 */
function return_value( $value ) {
    return $value;
}
