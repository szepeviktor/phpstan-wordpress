<?php

declare(strict_types=1);

// phpcs:disable Squiz.PHP.DiscouragedFunctions,NeutronStandard.Constants.DisallowDefine

// There are no core functions to read these constants.
define('ABSPATH', './');
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WPMU_PLUGIN_DIR', './');
define('EMPTY_TRASH_DAYS', 30 * 86400);

// Constants for expressing human-readable intervals.
define('MINUTE_IN_SECONDS', 60);
define('HOUR_IN_SECONDS', 60 * MINUTE_IN_SECONDS);
define('DAY_IN_SECONDS', 24 * HOUR_IN_SECONDS);
define('WEEK_IN_SECONDS', 7 * DAY_IN_SECONDS);
define('MONTH_IN_SECONDS', 30 * DAY_IN_SECONDS);
define('YEAR_IN_SECONDS', 365 * DAY_IN_SECONDS);

// Constants for expressing human-readable data sizes in their respective number of bytes.
define('KB_IN_BYTES', 1024);
define('MB_IN_BYTES', 1024 * KB_IN_BYTES);
define('GB_IN_BYTES', 1024 * MB_IN_BYTES);
define('TB_IN_BYTES', 1024 * GB_IN_BYTES);

// wpdb method parameters.
define('OBJECT', 'OBJECT');
define('OBJECT_K', 'OBJECT_K');
define('ARRAY_A', 'ARRAY_A');
define('ARRAY_N', 'ARRAY_N');
