<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function PHPStan\Testing\assertType;

// Default argument values
assertType('array<int, WP_Term>|WP_Error', get_terms());
assertType('array<int, WP_Term>|WP_Error', get_terms([]));

// Requesting a count
assertType('string|WP_Error', get_terms(['fields'=>'count']));
assertType('string|WP_Error', get_terms(['foo'=>'bar','fields'=>'count']));

// Requesting names or slugs
assertType('array<int, string>|WP_Error', get_terms(['fields'=>'names']));
assertType('array<int, string>|WP_Error', get_terms(['fields'=>'slugs']));
assertType('array<int, string>|WP_Error', get_terms(['fields'=>'id=>name']));
assertType('array<int, string>|WP_Error', get_terms(['fields'=>'id=>slug']));

// Requesting IDs
assertType('array<int, int>|WP_Error', get_terms(['fields'=>'ids']));
assertType('array<int, int>|WP_Error', get_terms(['fields'=>'tt_ids']));

// Requesting parent IDs (numeric strings)
assertType('array<int, numeric-string>|WP_Error', get_terms(['fields'=>'id=>parent']));

// Requesting objects
assertType('array<int, WP_Term>|WP_Error', get_terms(['fields'=>'all']));
assertType('array<int, WP_Term>|WP_Error', get_terms(['fields'=>'all_with_object_id']));
assertType('array<int, WP_Term>|WP_Error', get_terms(['fields'=>'foo']));

// Wrapper functions
assertType('string|WP_Error', wp_get_object_terms(123, 'category', ['fields'=>'count']));
assertType('string|WP_Error', wp_get_post_categories(123, ['fields'=>'count']));
assertType('string|WP_Error', wp_get_post_tags(123, ['fields'=>'count']));
assertType('string|WP_Error', wp_get_post_terms(123, 'category', ['fields'=>'count']));
assertType('array<int, WP_Term>|WP_Error', wp_get_object_terms(123, 'category'));
assertType('array<int, WP_Term>|WP_Error', wp_get_post_categories(123));
assertType('array<int, WP_Term>|WP_Error', wp_get_post_tags(123));
assertType('array<int, WP_Term>|WP_Error', wp_get_post_terms(123, 'category'));

// Unions - nonexhaustive list
$fields = $_GET['foo'] ? (string)$_GET['string'] : 'all';
$key = $_GET['foo'] ? (string)$_GET['string'] : 'fields';
$count = ! empty( $_GET['count'] );

$union = $_GET['foo'] ? ['key'=>'value'] : ['some'=>'thing'];
assertType('array<int, WP_Term>|WP_Error', get_terms($union));

$union = $_GET['foo'] ? ['key'=>'value'] : ['fields'=>'count'];
assertType('array<int, WP_Term>|string|WP_Error', get_terms($union));

$union = $_GET['foo'] ? ['key'=>'value'] : ['fields'=>'names'];
assertType('array<int, string|WP_Term>|WP_Error', get_terms($union));

$union = $_GET['foo'] ? ['key'=>'value'] : ['fields'=>'ids'];
assertType('array<int, int|WP_Term>|WP_Error', get_terms($union));

$union = $_GET['foo'] ? ['key'=>'value'] : ['fields'=>'id=>parent'];
assertType('array<int, WP_Term|numeric-string>|WP_Error', get_terms($union));

$union = $_GET['foo'] ? ['key'=>'value'] : ['fields'=>'all'];
assertType('array<int, WP_Term>|WP_Error', get_terms($union));

$union = $_GET['foo'] ? ['fields'=>'names'] : ['fields'=>'count'];
assertType('array<int, string>|string|WP_Error', get_terms($union));

$union = $_GET['foo'] ? ['fields'=>'id=>parent'] : ['fields'=>'ids'];
assertType('array<int, int|numeric-string>|WP_Error', get_terms($union));

$union = $_GET['foo'] ? ['fields'=>'all'] : ['fields'=>'count'];
assertType('array<int, WP_Term>|string|WP_Error', get_terms($union));

// Unions with unknown fields value
$union = $_GET['foo'] ? (string)$_GET['string'] : 'all';
assertType('array<int, int|string|WP_Term>|string|WP_Error', get_terms(['fields'=>$union]));

$union = $_GET['foo'] ? (string)$_GET['string'] : 'ids';
assertType('array<int, int|string|WP_Term>|string|WP_Error', get_terms(['fields'=>$union]));

$union = $_GET['foo'] ? (string)$_GET['string'] : 'id=>parent';
assertType('array<int, int|string|WP_Term>|string|WP_Error', get_terms(['fields'=>$union]));

$union = $_GET['foo'] ? (string)$_GET['string'] : 'count';
assertType('array<int, int|string|WP_Term>|string|WP_Error', get_terms(['fields'=>$union]));

$union = $_GET['foo'] ? (string)$_GET['string'] : 'names';
assertType('array<int, int|string|WP_Term>|string|WP_Error', get_terms(['fields'=>$union]));

$union = $_GET['foo'] ? (string)$_GET['string'] : 'all';
assertType('array<int, int|string|WP_Term>|string|WP_Error', get_terms(['foo'=>'bar','fields'=>$union]));

// Unions with unknown fields key - not supported
$union = $_GET['foo'] ? (string)$_GET['string'] : 'fields';
assertType('array<int, int|string|WP_Term>|string|WP_Error', get_terms([$union=>'all']));

$union = $_GET['foo'] ? (string)$_GET['string'] : 'fields';
assertType('array<int, int|string|WP_Term>|string|WP_Error', get_terms([$union=>'ids']));

$union = $_GET['foo'] ? (string)$_GET['string'] : 'fields';
assertType('array<int, int|string|WP_Term>|string|WP_Error', get_terms([$union=>'id=>parent']));

$union = $_GET['foo'] ? (string)$_GET['string'] : 'fields';
assertType('array<int, int|string|WP_Term>|string|WP_Error', get_terms([$union=>'count']));

$union = $_GET['foo'] ? (string)$_GET['string'] : 'fields';
assertType('array<int, int|string|WP_Term>|string|WP_Error', get_terms([$union=>'names']));

$union = $_GET['foo'] ? (string)$_GET['string'] : 'fields';
assertType('array<int, int|string|WP_Term>|string|WP_Error', get_terms(['foo'=>'bar',$union=>'all']));
