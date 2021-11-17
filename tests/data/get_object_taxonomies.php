<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function PHPStan\Testing\assertType;

// Default output
assertType('array<int, string>', get_object_taxonomies('post'));
assertType('array<int, string>', get_object_taxonomies('post', 'names'));
assertType('array<int, string>', get_object_taxonomies('post', 'Hello'));

// Unknown output
assertType('array<string|\WP_Taxonomy>', get_object_taxonomies('post', $_GET['foo']));

// Objects output
assertType('array<string, WP_Taxonomy>', get_object_taxonomies('post', 'objects'));
