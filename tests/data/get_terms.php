<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function PHPStan\Testing\assertType;

$fields = $_GET['fields'] ?? 'all';

// Default argument values
assertType('array<int, WP_Term>|WP_Error', get_terms());
assertType('array<int, WP_Term>|WP_Error', get_terms([]));
assertType('array<int, WP_Term>|WP_Error', get_terms(''));

// Unknown
assertType('array<int, WP_Term>|array<int, int>|array<int, string>|string|WP_Error', get_terms(['fields'=>$fields]));
assertType('array<int, WP_Term>|array<int, int>|array<int, string>|string|WP_Error', get_terms(['foo'=>'bar','fields'=>$fields]));
assertType('array<int, WP_Term>|array<int, int>|array<int, string>|string|WP_Error', get_terms("fields={$fields}"));

// Requesting a count
assertType('string|WP_Error', get_terms(['fields'=>'count']));
assertType('string|WP_Error', get_terms(['foo'=>'bar','fields'=>'count']));
assertType('string|WP_Error', get_terms('fields=count'));
