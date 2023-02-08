<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function PHPStan\Testing\assertType;

// Default value of true
assertType('void', get_search_form());
assertType('void', the_title_attribute());
assertType('void', wp_dropdown_categories());
assertType('void', wp_dropdown_languages());
assertType('void', wp_dropdown_pages());
assertType('void', wp_dropdown_users());
assertType('void', wp_get_archives());
assertType('void', wp_link_pages());
assertType('void', wp_list_authors());
assertType('void', wp_list_bookmarks());
assertType('void|false', wp_list_categories());
assertType('void', wp_list_comments());
assertType('void', wp_list_pages());
assertType('void', wp_list_users());
assertType('void', wp_login_form());
assertType('void', wp_page_menu());

// Explicit value of true
$args = ['echo' => true];
assertType('void', get_search_form($args));
assertType('void', the_title_attribute($args));
assertType('void', wp_dropdown_categories($args));
assertType('void', wp_dropdown_languages($args));
assertType('void', wp_dropdown_pages($args));
assertType('void', wp_dropdown_users($args));
assertType('void', wp_get_archives($args));
assertType('void', wp_link_pages($args));
assertType('void', wp_list_authors($args));
assertType('void', wp_list_bookmarks($args));
assertType('void|false', wp_list_categories($args));
assertType('void', wp_list_comments($args));
assertType('void', wp_list_pages($args));
assertType('void', wp_list_users($args));
assertType('void', wp_login_form($args));
assertType('void', wp_page_menu($args));

// Explicit value of 1
$args = ['echo' => 1];
assertType('void', wp_dropdown_categories($args));
assertType('void', wp_dropdown_languages($args));
assertType('void', wp_dropdown_pages($args));
assertType('void', wp_dropdown_users($args));
assertType('void', wp_get_archives($args));
assertType('void', wp_link_pages($args));
assertType('void', wp_list_bookmarks($args));
assertType('void|false', wp_list_categories($args));

// Explicit value of false
$args = ['echo' => false];
assertType('string', get_search_form($args));
assertType('string|void', the_title_attribute($args));
assertType('string', wp_dropdown_categories($args));
assertType('string|void', wp_dropdown_languages($args));
assertType('string', wp_dropdown_pages($args));
assertType('string', wp_dropdown_users($args));
assertType('string|void', wp_get_archives($args));
assertType('string', wp_link_pages($args));
assertType('string', wp_list_authors($args));
assertType('string', wp_list_bookmarks($args));
assertType('string|false', wp_list_categories($args));
assertType('string|void', wp_list_comments($args));
assertType('string', wp_list_pages($args));
assertType('string', wp_list_users($args));
assertType('string', wp_login_form($args));
assertType('string', wp_page_menu($args));
