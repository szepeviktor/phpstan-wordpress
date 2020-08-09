<?php

declare(strict_types=1);

// phpcs:disable Squiz.PHP.DiscouragedFunctions,NeutronStandard.Constants.DisallowDefine

// Directory constants.
define('ABSPATH', './');
define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins');
define('WPMU_PLUGIN_DIR', WP_CONTENT_DIR . '/mu-plugins');

// URL constants.
define('WP_CONTENT_URL', 'http://localhost/wp-content');
define('WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins');
define('WPMU_PLUGIN_URL', WP_CONTENT_URL . '/mu-plugins');

// Debug constants.
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', true);
define('WP_DEBUG_LOG', false);
define('WP_CACHE', false);
define('SCRIPT_DEBUG', false);

// WP underground constants - random value picked
define('WP_START_TIMESTAMP', microtime(true));
define('WP_MEMORY_LIMIT', '64M');
define('WP_MAX_MEMORY_LIMIT', '256M');
define('EMPTY_TRASH_DAYS', 30 * 86400);
define('MEDIA_TRASH', false);
define('SHORTINIT', false);
define('WP_FEATURE_BETTER_PASSWORDS', true);
define('FORCE_SSL_ADMIN', true);
define('AUTOSAVE_INTERVAL', MINUTE_IN_SECONDS);
define('WP_POST_REVISIONS', true);
define('WP_CRON_LOCK_TIMEOUT', MINUTE_IN_SECONDS);
define('WP_DEFAULT_THEME', 'twentytwenty');
define('TEMPLATEPATH', WP_CONTENT_DIR . '/themes/twentytwenty');
define('STYLESHEETPATH', WP_CONTENT_DIR . '/themes/twentytwenty');

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

// Wpdb method parameters.
define('OBJECT', 'OBJECT');
define('OBJECT_K', 'OBJECT_K');
define('ARRAY_A', 'ARRAY_A');
define('ARRAY_N', 'ARRAY_N');

// Cookies constants.
define('COOKIEHASH', md5('http://localhost'));
define('USER_COOKIE', 'wordpressuser_'. COOKIEHASH);
define('PASS_COOKIE', 'wordpresspass_'. COOKIEHASH);
define('AUTH_COOKIE', 'wordpress_'. COOKIEHASH);
define('SECURE_AUTH_COOKIE', 'wordpress_sec_'. COOKIEHASH);
define('LOGGED_IN_COOKIE', 'wordpress_logged_in_'. COOKIEHASH);
define('TEST_COOKIE', 'wordpress_test_cookie');
define('COOKIEPATH', '/');
define('SITECOOKIEPATH', '/');
define('ADMIN_COOKIE_PATH', SITECOOKIEPATH . 'wp-admin');
define('PLUGINS_COOKIE_PATH', preg_replace('|https?://[^/]+|i', '', WP_PLUGIN_URL));
define('COOKIE_DOMAIN', false);
define('RECOVERY_MODE_COOKIE', 'wordpress_rec_' . COOKIEHASH);
