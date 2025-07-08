<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use const ABSPATH;
use const WP_CONTENT_DIR;
use const WP_LANG_DIR;
use const WPMU_PLUGIN_DIR;
use const WP_PLUGIN_DIR;
use const WP_DEBUG;
use const WP_DEBUG_DISPLAY;
use const EMPTY_TRASH_DAYS;
use const SCRIPT_DEBUG;
use const COOKIE_DOMAIN;
use const WP_DEFAULT_THEME;
use const MINUTE_IN_SECONDS;
use const HOUR_IN_SECONDS;
use const DAY_IN_SECONDS;
use const WEEK_IN_SECONDS;
use const MONTH_IN_SECONDS;
use const YEAR_IN_SECONDS;
use const KB_IN_BYTES;
use const MB_IN_BYTES;
use const GB_IN_BYTES;
use const TB_IN_BYTES;
use const PB_IN_BYTES;
use const EB_IN_BYTES;
use const OBJECT;
use const OBJECT_K;
use const ARRAY_A;
use const ARRAY_N;
use const EP_NONE;
use const EP_PERMALINK;
use const EP_ATTACHMENT;
use const EP_DATE;
use const EP_YEAR;
use const EP_MONTH;
use const EP_DAY;
use const EP_ROOT;
use const EP_COMMENTS;
use const EP_SEARCH;
use const EP_CATEGORIES;
use const EP_TAGS;
use const EP_AUTHORS;
use const EP_PAGES;
use const EP_ALL_ARCHIVES;
use const EP_ALL;
use const FS_CONNECT_TIMEOUT;
use const FS_TIMEOUT;
use const FS_CHMOD_DIR;
use const FS_CHMOD_FILE;

use function PHPStan\Testing\assertType;

/*
 * Unconditional constants resolve to constant values and are
 * - defined in bootstrap.php
 * - NOT part of the dynamicConstantNames list in extension.neon.
 */

assertType('60', MINUTE_IN_SECONDS);
assertType('3600', HOUR_IN_SECONDS);
assertType('86400', DAY_IN_SECONDS);
assertType('604800', WEEK_IN_SECONDS);
assertType('2592000', MONTH_IN_SECONDS);
assertType('31536000', YEAR_IN_SECONDS);

assertType('1024', KB_IN_BYTES);
assertType('1048576', MB_IN_BYTES);
assertType('1073741824', GB_IN_BYTES);

if (PHP_INT_SIZE === 8) {
    // 64bit
    assertType('1099511627776', TB_IN_BYTES);
    assertType('1125899906842624', PB_IN_BYTES);
    assertType('1152921504606846976', EB_IN_BYTES);
    // ZB_IN_BYTES and YB_IN_BYTES will be converted to floats.
}

assertType("'OBJECT'", OBJECT);
assertType("'OBJECT_K'", OBJECT_K);
assertType("'ARRAY_A'", ARRAY_A);
assertType("'ARRAY_N'", ARRAY_N);

assertType('0', EP_NONE);
assertType('1', EP_PERMALINK);
assertType('2', EP_ATTACHMENT);
assertType('4', EP_DATE);
assertType('8', EP_YEAR);
assertType('16', EP_MONTH);
assertType('32', EP_DAY);
assertType('64', EP_ROOT);
assertType('128', EP_COMMENTS);
assertType('256', EP_SEARCH);
assertType('512', EP_CATEGORIES);
assertType('1024', EP_TAGS);
assertType('2048', EP_AUTHORS);
assertType('4096', EP_PAGES);
assertType('3644', EP_ALL_ARCHIVES);
assertType('8191', EP_ALL);

/*
 * Conditional constants resolve to non constant types and are
 * - defined in bootstrap.php
 * - part of the dynamicConstantNames list in extension.neon.
 */

assertType('bool', WP_DEBUG);
// assertType('bool|string', WP_DEBUG_LOG); // PHPStan does not yet support more than one type for constants
assertType('bool', WP_DEBUG_DISPLAY);

assertType('string', ABSPATH);
assertType('string', WP_CONTENT_DIR);
assertType('string', WP_PLUGIN_DIR);
assertType('string', WPMU_PLUGIN_DIR);
assertType('string', WP_LANG_DIR);

assertType('int', FS_CONNECT_TIMEOUT);
assertType('int', FS_TIMEOUT);
assertType('int', FS_CHMOD_DIR);
assertType('int', FS_CHMOD_FILE);

assertType('string', COOKIE_DOMAIN);
assertType('int', EMPTY_TRASH_DAYS);
assertType('bool', SCRIPT_DEBUG);
assertType('string', WP_DEFAULT_THEME);
