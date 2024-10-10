<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function _get_list_table;
use function PHPStan\Testing\assertType;

// Known class name
assertType('WP_Posts_List_Table', _get_list_table('WP_Posts_List_Table'));
assertType('false', _get_list_table('Unknown_Table'));

// Unknown class name
/** @var class-string $classString */
assertType('WP_List_Table|false', _get_list_table($classString));
