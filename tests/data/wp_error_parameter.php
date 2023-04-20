<?php

/**
 * Test data for WPErrorParameterDynamicFunctionReturnTypeExtension.
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function PHPStan\Testing\assertType;
use function _set_cron_array;

/**
 * _set_cron_array()
 */
$value = _set_cron_array([]);
assertType('bool', $value);

$value = _set_cron_array([], false);
assertType('bool', $value);

$value = _set_cron_array([], true);
assertType('WP_Error|true', $value);

$value = _set_cron_array([], $_GET['wp_error']);
assertType('bool|WP_Error', $value);
