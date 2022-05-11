<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_error_reporting
error_reporting(E_ALL);

// extension.neon contains a relative link.
mkdir(dirname(__DIR__) . '/vendor/vendorName/packageName', 0777, true);
copy(dirname(__DIR__) . '/extension.neon', dirname(__DIR__) . '/vendor/vendorName/packageName/extension.neon');
copy(dirname(__DIR__) . '/bootstrap.php', dirname(__DIR__) . '/vendor/vendorName/packageName/bootstrap.php');

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once __DIR__ . '/functions.php';
