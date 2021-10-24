<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function PHPStan\Testing\assertType;

// Known class name
assertType('WP_Posts_List_Table|false', _get_list_table('WP_Posts_List_Table'));
assertType('Unknown_Table|false', _get_list_table('Unknown_Table'));

// Unknown class name
assertType('\WP_List_Table|false', _get_list_table(_GET['foo']));
