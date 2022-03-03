<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function PHPStan\Testing\assertType;

$fields = $_GET['fields'] ?? 'all';

// Default argument values
assertType('array<int, WP_Term>|WP_Error', get_terms());
assertType('array<int, WP_Term>|WP_Error', get_terms([]));
assertType('array<int, WP_Term>|WP_Error', get_terms(''));

// Requesting a count
assertType('string|WP_Error', get_terms(['fields'=>'count']));
assertType('string|WP_Error', get_terms(['foo'=>'bar','fields'=>'count']));
assertType('string|WP_Error', get_terms('fields=count'));
