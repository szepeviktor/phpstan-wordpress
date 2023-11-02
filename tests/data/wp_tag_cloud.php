<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function wp_tag_cloud;
use function PHPStan\Testing\assertType;

// Default
assertType('void', wp_tag_cloud());

// Unknown
assertType('array<int, string>|string|void', wp_tag_cloud($_GET['foo']));

// Vary $args['echo'] with default $args['format']
assertType('void', wp_tag_cloud(['echo' => true]));
assertType('string|void', wp_tag_cloud(['echo' => false]));
assertType('string|void', wp_tag_cloud(['echo' => $_GET['foo']]));

// Vary $args['format'] with default $args['echo']
assertType('array<int, string>|void', wp_tag_cloud(['format' => 'array']));
assertType('void', wp_tag_cloud(['format' => 'flat']));
assertType('array<int, string>|void', wp_tag_cloud(['format' => $_GET['foo']]));

// Vary $args['format'] with $args['echo'] = true
assertType('array<int, string>|void', wp_tag_cloud(['echo' => true, 'format' => 'array']));
assertType('void', wp_tag_cloud(['echo' => true, 'format' => 'flat']));
assertType('array<int, string>|void', wp_tag_cloud(['echo' => true, 'format' => $_GET['foo']]));

// Vary $args['format'] with $args['echo'] = false
assertType('array<int, string>|void', wp_tag_cloud(['echo' => false, 'format' => 'array']));
assertType('string|void', wp_tag_cloud(['echo' => false, 'format' => 'flat']));
assertType('array<int, string>|string|void', wp_tag_cloud(['echo' => false, 'format' => $_GET['foo']]));

// Vary $args['format'] with unknown $args['echo']
assertType('array<int, string>|void', wp_tag_cloud(['echo' => $_GET['foo'], 'format' => 'array']));
assertType('string|void', wp_tag_cloud(['echo' => $_GET['foo'], 'format' => 'flat']));
assertType('array<int, string>|string|void', wp_tag_cloud(['echo' => $_GET['foo'], 'format' => $_GET['baz']]));
