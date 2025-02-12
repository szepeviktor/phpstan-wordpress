<?php

/**
 * Custom rule to discourage using WordPress constants fetchable via functions.
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleErrorBuilder;

use function array_key_exists;
use function sprintf;

/**
 * @implements \PHPStan\Rules\Rule<\PhpParser\Node\Expr\ConstFetch>
 */
final class WpConstantFetchRule implements \PHPStan\Rules\Rule
{
    protected const REPLACEMENTS = [
        'BACKGROUND_COLOR' => "get_theme_support('custom-background')",
        'BACKGROUND_IMAGE' => "get_theme_support('custom-background')",
        'DISALLOW_FILE_MODS' => 'wp_is_file_mod_allowed()',
        'DOING_AJAX' => 'wp_doing_ajax()',
        'DOING_CRON' => 'wp_doing_cron()',
        'FORCE_SSL_ADMIN' => 'force_ssl_admin()',
        'FORCE_SSL_LOGIN' => 'force_ssl_admin()',
        'FS_METHOD' => 'get_filesystem_method()',
        'HEADER_IMAGE' => "get_theme_support('custom-header')",
        'HEADER_IMAGE_WIDTH' => "get_theme_support('custom-header')",
        'HEADER_IMAGE_HEIGHT' => "get_theme_support('custom-header')",
        'HEADER_TEXTCOLOR' => "get_theme_support('custom-header')",
        'MULTISITE' => 'is_multisite()',
        'NO_HEADER_TEXT' => "get_theme_support('custom-header')",
        'STYLESHEETPATH' => 'get_stylesheet_directory()',
        'SUBDOMAIN_INSTALL' => 'is_subdomain_install()',
        'TEMPLATEPATH' => 'get_template_directory()',
        'UPLOADS' => 'wp_get_upload_dir() or wp_upload_dir(null, false)',
        'VHOST' => 'is_subdomain_install()',
        'WP_ADMIN' => 'is_admin()',
        'WP_BLOG_ADMIN' => 'is_blog_admin()',
        'WP_CONTENT_URL' => 'content_url()',
        'WP_DEVELOPMENT_MODE' => 'wp_get_development_mode()',
        'WP_HOME' => 'home_url() or get_home_url()',
        'WP_INSTALLING' => 'wp_installing()',
        'WP_NETWORK_ADMIN' => 'is_network_admin()',
        'WP_PLUGIN_URL' => "plugins_url() or plugin_dir_url('')",
        'WP_POST_REVISIONS' => 'wp_revisions_to_keep()',
        'WP_SITEURL' => 'site_url() or get_site_url()',
        'WP_USER_ADMIN' => 'is_user_admin()',
        'WP_USE_THEMES' => 'wp_using_themes()',
        'WPMU_PLUGIN_URL' => 'plugins_url()',
    ];

    public function getNodeType(): string
    {
        return Node\Expr\ConstFetch::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (! $this->isDiscouragedConstant($node->name)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                sprintf(
                    'Found usage of constant %s. Use %s instead.',
                    (string)$node->name,
                    $this->getReplacement($node->name)
                )
            )
                ->identifier('phpstanWP.wpConstant.fetch')
                ->build(),
        ];
    }

    private function isDiscouragedConstant(Node\Name $constantName): bool
    {
        return array_key_exists((string)$constantName, self::REPLACEMENTS);
    }

    private function getReplacement(Node\Name $constantName): string
    {
        return self::REPLACEMENTS[(string)$constantName];
    }
}
