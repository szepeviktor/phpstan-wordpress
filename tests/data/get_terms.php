<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function PHPStan\Testing\assertType;

$fields = $_GET['fields'] ?? 'all';
$key = $_GET['key'] ?? 'fields';

// Default argument values
assertType('array<int, WP_Term>|WP_Error', get_terms());
assertType('array<int, WP_Term>|WP_Error', get_terms([]));

// Unknown
assertType('array<int, WP_Term>|array<int, int>|array<int, string>|string|WP_Error', get_terms(['fields'=>$fields]));
assertType('array<int, WP_Term>|array<int, int>|array<int, string>|string|WP_Error', get_terms(['foo'=>'bar','fields'=>$fields]));

// Unknown keys
assertType('array<int, int|string|WP_Term>|string|WP_Error', get_terms([$key=>'all']));
assertType('array<int, int|string|WP_Term>|string|WP_Error', get_terms(['foo'=>'bar',$key=>'all']));

// Requesting a count
assertType('string|WP_Error', get_terms(['fields'=>'count']));
assertType('string|WP_Error', get_terms(['foo'=>'bar','fields'=>'count']));
assertType('string|WP_Error', get_terms(['count'=>true]));
assertType('string|WP_Error', get_terms(['foo'=>'bar','count'=>true]));
