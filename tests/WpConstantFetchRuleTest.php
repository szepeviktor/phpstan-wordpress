<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use PHPStan\Rules\Rule;
use SzepeViktor\PHPStan\WordPress\WpConstantFetchRule;

/**
 * @extends \PHPStan\Testing\RuleTestCase<\SzepeViktor\PHPStan\WordPress\WpConstantFetchRule>
 */
class WpConstantFetchRuleTest extends \PHPStan\Testing\RuleTestCase
{
    protected function getRule(): Rule
    {
        return new WpConstantFetchRule();
    }

    public function testRule(): void
    {
        $this->analyse(
            [
                __DIR__ . '/data/wp-constants.php',
                __DIR__ . '/data/internal-error.php',
            ],
            [
                ["Found usage of constant BACKGROUND_COLOR. Use get_theme_support('custom-background') instead.", 9],
                ["Found usage of constant BACKGROUND_IMAGE. Use get_theme_support('custom-background') instead.", 10],
                ['Found usage of constant DISALLOW_FILE_MODS. Use wp_is_file_mod_allowed() instead.', 11],
                ['Found usage of constant DOING_AJAX. Use wp_doing_ajax() instead.', 12],
                ['Found usage of constant DOING_CRON. Use wp_doing_cron() instead.', 13],
                ['Found usage of constant FORCE_SSL_ADMIN. Use force_ssl_admin() instead.', 14],
                ['Found usage of constant FORCE_SSL_LOGIN. Use force_ssl_admin() instead.', 15],
                ['Found usage of constant FS_METHOD. Use get_filesystem_method() instead.', 16],
                ['Found usage of constant MULTISITE. Use is_multisite() instead.', 17],
                ['Found usage of constant STYLESHEETPATH. Use get_stylesheet_directory() instead.', 18],
                ['Found usage of constant SUBDOMAIN_INSTALL. Use is_subdomain_install() instead.', 19],
                ['Found usage of constant TEMPLATEPATH. Use get_template_directory() instead.', 20],
                ['Found usage of constant UPLOADS. Use wp_get_upload_dir() or wp_upload_dir(null, false) instead.', 21],
                ['Found usage of constant VHOST. Use is_subdomain_install() instead.', 22],
                ['Found usage of constant WP_ADMIN. Use is_admin() instead.', 23],
                ['Found usage of constant WP_BLOG_ADMIN. Use is_blog_admin() instead.', 24],
                ['Found usage of constant WP_CONTENT_URL. Use content_url() instead.', 25],
                ['Found usage of constant WP_DEVELOPMENT_MODE. Use wp_get_development_mode() instead.', 26],
                ['Found usage of constant WP_HOME. Use home_url() or get_home_url() instead.', 27],
                ['Found usage of constant WP_INSTALLING. Use wp_installing() instead.', 28],
                ['Found usage of constant WP_NETWORK_ADMIN. Use is_network_admin() instead.', 29],
                ["Found usage of constant WP_PLUGIN_URL. Use plugins_url() or plugin_dir_url('') instead.", 30],
                ['Found usage of constant WP_POST_REVISIONS. Use wp_revisions_to_keep() instead.', 31],
                ['Found usage of constant WP_SITEURL. Use site_url() or get_site_url() instead.', 32],
                ['Found usage of constant WP_USER_ADMIN. Use is_user_admin() instead.', 33],
                ['Found usage of constant WP_USE_THEMES. Use wp_using_themes() instead.', 34],
                ['Found usage of constant WPMU_PLUGIN_URL. Use plugins_url() instead.', 35],
            ]
        );
    }

    public static function getAdditionalConfigFiles(): array
    {
        return [dirname(__DIR__) . '/vendor/szepeviktor/phpstan-wordpress/extension.neon'];
    }
}
