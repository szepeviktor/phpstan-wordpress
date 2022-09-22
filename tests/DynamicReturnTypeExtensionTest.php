<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

class DynamicReturnTypeExtensionTest extends \PHPStan\Testing\TypeInferenceTestCase
{
    /**
     * @return iterable<mixed>
     */
    public function dataFileAsserts(): iterable
    {
        // Path to a file with actual asserts of expected types:
        yield from $this->gatherAssertTypes(__DIR__ . '/data/_get_list_table.php');
        yield from $this->gatherAssertTypes(__DIR__ . '/data/apply_filters.php');
        yield from $this->gatherAssertTypes(__DIR__ . '/data/current_time.php');
        yield from $this->gatherAssertTypes(__DIR__ . '/data/echo_parameter.php');
        yield from $this->gatherAssertTypes(__DIR__ . '/data/esc_sql.php');
        yield from $this->gatherAssertTypes(__DIR__ . '/data/get_comment.php');
        yield from $this->gatherAssertTypes(__DIR__ . '/data/get_object_taxonomies.php');
        yield from $this->gatherAssertTypes(__DIR__ . '/data/get_post.php');
        yield from $this->gatherAssertTypes(__DIR__ . '/data/get_posts.php');
        yield from $this->gatherAssertTypes(__DIR__ . '/data/get_sites.php');
        yield from $this->gatherAssertTypes(__DIR__ . '/data/get_terms.php');
        yield from $this->gatherAssertTypes(__DIR__ . '/data/has_filter.php');
        yield from $this->gatherAssertTypes(__DIR__ . '/data/mysql2date.php');
        yield from $this->gatherAssertTypes(__DIR__ . '/data/shortcode_atts.php');
        yield from $this->gatherAssertTypes(__DIR__ . '/data/term_exists.php');
        yield from $this->gatherAssertTypes(__DIR__ . '/data/wp_error_parameter.php');
        yield from $this->gatherAssertTypes(__DIR__ . '/data/wp_parse_url.php');
        yield from $this->gatherAssertTypes(__DIR__ . '/data/wp_theme_get.php');
    }

    /**
     * @dataProvider dataFileAsserts
     * @param array<string> ...$args
     */
    public function testFileAsserts(string $assertType, string $file, ...$args): void
    {
        $this->assertFileAsserts($assertType, $file, ...$args);
    }

    public static function getAdditionalConfigFiles(): array
    {
        return [dirname(__DIR__) . '/vendor/szepeviktor/phpstan-wordpress/extension.neon'];
    }
}
