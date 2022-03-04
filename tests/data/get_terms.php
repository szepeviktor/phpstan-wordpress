<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function PHPStan\Testing\assertType;

$fields = $_GET['fields'] ?? 'all';
$key = $_GET['key'] ?? 'fields';

// Default argument values
assertType('array<int, WP_Term>|WP_Error', get_terms());
assertType('array<int, WP_Term>|WP_Error', get_terms([]));

// Unknown values
assertType('array<int, int|string|WP_Term>|string|WP_Error', get_terms(['fields'=>$fields]));
assertType('array<int, int|string|WP_Term>|string|WP_Error', get_terms(['foo'=>'bar','fields'=>$fields]));

// Unknown keys
assertType('array<int, int|string|WP_Term>|string|WP_Error', get_terms([$key=>'all']));
assertType('array<int, int|string|WP_Term>|string|WP_Error', get_terms(['foo'=>'bar',$key=>'all']));

// Requesting a count
assertType('string|WP_Error', get_terms(['fields'=>'count']));
assertType('string|WP_Error', get_terms(['foo'=>'bar','fields'=>'count']));
assertType('string|WP_Error', get_terms(['count'=>true]));
assertType('string|WP_Error', get_terms(['foo'=>'bar','count'=>true]));
assertType('string|WP_Error', get_terms(['fields'=>'ids','count'=>true]));

// Requesting names or slugs
assertType('array<int, string>|WP_Error', get_terms(['fields'=>'names']));
assertType('array<int, string>|WP_Error', get_terms(['fields'=>'slugs']));
assertType('array<int, string>|WP_Error', get_terms(['fields'=>'id=>name']));
assertType('array<int, string>|WP_Error', get_terms(['fields'=>'id=>slug']));

// Requesting IDs
assertType('array<int, int>|WP_Error', get_terms(['fields'=>'ids']));
assertType('array<int, int>|WP_Error', get_terms(['fields'=>'tt_ids']));

// Requesting parent IDs (numeric strings)
assertType('array<int, string>|WP_Error', get_terms(['fields'=>'id=>parent']));

// Requesting objects
assertType('array<int, WP_Term>|WP_Error', get_terms(['fields'=>'all']));
assertType('array<int, WP_Term>|WP_Error', get_terms(['fields'=>'all_with_object_id']));
assertType('array<int, WP_Term>|WP_Error', get_terms(['fields'=>'foo']));
