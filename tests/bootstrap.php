<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_error_reporting
error_reporting(E_ALL);

// extension.neon contains relative links.
$helperDirectory = dirname(__DIR__) . '/vendor/szepeviktor/phpstan-wordpress';
if (! is_dir($helperDirectory)) {
    mkdir($helperDirectory, 0777, true);
}
copy(dirname(__DIR__) . '/extension.neon', $helperDirectory . '/extension.neon');
copy(dirname(__DIR__) . '/bootstrap.php', $helperDirectory . '/bootstrap.php');

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once __DIR__ . '/functions.php';
