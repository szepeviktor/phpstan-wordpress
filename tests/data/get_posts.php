<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function PHPStan\Testing\assertType;

assertType('array<int, WP_Post>', get_posts());
assertType('array<int, WP_Post>', get_posts(['key' => 'value']));
assertType('array<int, WP_Post>', get_posts(['fields' => '']));
assertType('array<int, int>', get_posts(['fields' => 'ids']));
assertType('array<int, int>', get_posts(['fields' => 'id=>parent']));
assertType('array<int, WP_Post>', get_posts(['fields' => 'Hello']));

// Nonconstant array
assertType('array<int, int|WP_Post>', get_posts((array)$_GET['array']));

// Unions
$union = $_GET['foo'] ? ['key' => 'value'] : ['some' => 'thing'];
assertType('array<int, WP_Post>', get_posts($union));

$union = $_GET['foo'] ? ['key' => 'value'] : ['fields' => 'ids'];
assertType('array<int, int|WP_Post>', get_posts($union));

$union = $_GET['foo'] ? ['key' => 'value'] : ['fields' => ''];
assertType('array<int, WP_Post>', get_posts($union));

$union = $_GET['foo'] ? ['key' => 'value'] : ['fields' => 'id=>parent'];
assertType('array<int, int|WP_Post>', get_posts($union));

$union = $_GET['foo'] ? ['fields' => ''] : ['fields' => 'ids'];
assertType('array<int, int|WP_Post>', get_posts($union));

$union = $_GET['foo'] ? ['fields' => ''] : ['fields' => 'id=>parent'];
assertType('array<int, int|WP_Post>', get_posts($union));

$union = $_GET['foo'] ? ['fields' => 'ids'] : ['fields' => 'id=>parent'];
assertType('array<int, int>', get_posts($union));

$union = $_GET['foo'] ? (array)$_GET['array'] : ['fields' => ''];
assertType('array<int, int|WP_Post>', get_posts($union));

$union = $_GET['foo'] ? (array)$_GET['array'] : ['fields' => 'ids'];
assertType('array<int, int|WP_Post>', get_posts($union));

$union = $_GET['foo'] ? (array)$_GET['array'] : ['fields' => 'id=>parent'];
assertType('array<int, int|WP_Post>', get_posts($union));

$union = $_GET['foo'] ? (string)$_GET['string'] : '';
assertType('array<int, int|WP_Post>', get_posts(['fields' => $union]));

$union = $_GET['foo'] ? (string)$_GET['string'] : 'ids';
assertType('array<int, int|WP_Post>', get_posts(['fields' => $union]));

$union = $_GET['foo'] ? (string)$_GET['string'] : 'id=>parent';
assertType('array<int, int|WP_Post>', get_posts(['fields' => $union]));

$union = $_GET['foo'] ? (string)$_GET['string'] : 'fields';
assertType('array<int, int|WP_Post>', get_posts([$union => '']));

$union = $_GET['foo'] ? (string)$_GET['string'] : 'fields';
assertType('array<int, int|WP_Post>', get_posts([$union => 'ids']));

$union = $_GET['foo'] ? (string)$_GET['string'] : 'fields';
assertType('array<int, int|WP_Post>', get_posts([$union => 'id=>parent']));
