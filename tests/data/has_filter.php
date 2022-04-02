<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function PHPStan\Testing\assertType;

// Default callback of false
assertType('bool', has_filter(''));
assertType('bool', has_action(''));

// Explicit callback of false
assertType('bool', has_filter('', false));
assertType('bool', has_action('', false));

// Explicit callback
assertType('int|false', has_filter('', 'intval'));
assertType('int|false', has_action('', 'intval'));

// Unknown callback
$callback = $_GET['callback'] ?? 'foo';
assertType('bool|int', has_filter('', $callback));
assertType('bool|int', has_action('', $callback));
