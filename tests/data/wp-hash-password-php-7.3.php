<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function PHPStan\Testing\assertType;
use function wp_hash_password;

/*
 * PHP 7.3: password_hash() may return false or null on failure.
 */

assertType('non-falsy-string|false|null', wp_hash_password('password'));

/** @var string $password */
assertType('non-falsy-string|false|null', wp_hash_password($password));
