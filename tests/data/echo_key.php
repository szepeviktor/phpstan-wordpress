<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function PHPStan\Testing\assertType;

// Default value of true
assertType('void', get_search_form());
assertType('void', the_title_attribute());
assertType('void', wp_get_archives());
assertType('void', wp_list_authors());
assertType('void', wp_list_bookmarks());
assertType('void|false', wp_list_categories());
assertType('void', wp_list_comments());
assertType('void', wp_list_pages());
assertType('void', wp_list_users());
assertType('void', wp_login_form());
assertType('void', wp_page_menu());

// Explicit array key value of true
$args = ['echo' => true];
assertType('void', get_search_form($args));
assertType('void', the_title_attribute($args));
assertType('void', wp_get_archives($args));
assertType('void', wp_list_authors($args));
assertType('void', wp_list_bookmarks($args));
assertType('void|false', wp_list_categories($args));
assertType('void', wp_list_comments($args));
assertType('void', wp_list_pages($args));
assertType('void', wp_list_users($args));
assertType('void', wp_login_form($args));
assertType('void', wp_page_menu($args));

// Explicit array key value of 1
$args = ['echo' => 1];
assertType('void', wp_get_archives($args));
assertType('void', wp_list_bookmarks($args));
assertType('void|false', wp_list_categories($args));

// Explicit array key value of false
$args = ['echo' => false];
assertType('string', get_search_form($args));
assertType('string|void', the_title_attribute($args));
assertType('string|void', wp_get_archives($args));
assertType('string', wp_list_authors($args));
assertType('string', wp_list_bookmarks($args));
assertType('string|false', wp_list_categories($args));
assertType('string|void', wp_list_comments($args));
assertType('string', wp_list_pages($args));
assertType('string', wp_list_users($args));
assertType('string', wp_login_form($args));
assertType('string', wp_page_menu($args));

// Explicit array key value of 0
$args = ['echo' => 0];
assertType('string|void', wp_get_archives($args));
assertType('string', wp_list_bookmarks($args));
assertType('string|false', wp_list_categories($args));

// Unknown array key value
$args = ['echo' => $_GET['foo']];
assertType('string|void', get_search_form($args));
assertType('string|void', the_title_attribute($args));
assertType('string|void', wp_list_authors($args));
assertType('string|void', wp_list_comments($args));
assertType('string|void', wp_list_users($args));
assertType('string|void', wp_login_form($args));
assertType('string|void', wp_page_menu($args));

// Explicit no query string value
$args = 'akey=avalue';
assertType('void', the_title_attribute($args));
assertType('void', wp_list_authors($args));
assertType('void', wp_list_comments($args));
assertType('void', wp_list_users($args));
assertType('void', wp_page_menu($args));

// Explicit non empty non numeric query string value (includes 'true' & 'false')
$args = 'echo=nonemptynonnumeric&akey=avalue';
assertType('void', the_title_attribute($args));
assertType('void', wp_list_authors($args));
assertType('void', wp_list_comments($args));
assertType('void', wp_list_users($args));
assertType('void', wp_page_menu($args));

// Explicit non zero numeric query string value
$args = 'echo=1&akey=avalue';
assertType('void', the_title_attribute($args));
assertType('void', wp_list_authors($args));
assertType('void', wp_list_comments($args));
assertType('void', wp_list_users($args));
assertType('void', wp_page_menu($args));

// Explicit query string value of 0
$args = 'echo=0&akey=avalue';
assertType('string|void', the_title_attribute($args));
assertType('string', wp_list_authors($args));
assertType('string|void', wp_list_comments($args));
assertType('string', wp_list_users($args));
assertType('string', wp_page_menu($args));

// Explicit empty query string value
$args = 'echo=&akey=avalue';
assertType('string|void', the_title_attribute($args));
assertType('string', wp_list_authors($args));
assertType('string|void', wp_list_comments($args));
assertType('string', wp_list_users($args));
assertType('string', wp_page_menu($args));

// Unknown value
$args = $_GET['foo'];
assertType('string|void', get_search_form($args));
assertType('string|void', the_title_attribute($args));
assertType('string|void', wp_list_authors($args));
assertType('string|void', wp_list_comments($args));
assertType('string|void', wp_list_users($args));
assertType('string|void', wp_login_form($args));
assertType('string|void', wp_page_menu($args));

// Explicit query string value of 0 or 1 is the same as unknown
/** @var 'echo=0&akey=avalue'|'echo=1&akey=avalue' $args */
$args = '';
assertType('string|void', get_search_form($args));
assertType('string|void', the_title_attribute($args));
assertType('string|void', wp_list_authors($args));
assertType('string|void', wp_list_comments($args));
assertType('string|void', wp_list_users($args));
assertType('string|void', wp_login_form($args));
assertType('string|void', wp_page_menu($args));
