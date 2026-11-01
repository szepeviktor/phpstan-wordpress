<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function PHPStan\Testing\assertType;
use function wp_hash_password;

/*
 * PHP 7.4 to 8.4: union of the PHP 7.4 (null on failure) and PHP 8 (throws) results.
 */

assertType('non-falsy-string|null', wp_hash_password('password'));

/** @var string $password */
assertType('non-falsy-string|null', wp_hash_password($password));
