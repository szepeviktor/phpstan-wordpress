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
        yield from self::gatherAssertTypes(__DIR__ . '/data/_get_list_table.php');
        yield from self::gatherAssertTypes(__DIR__ . '/data/apply_filters.php');
        yield from self::gatherAssertTypes(__DIR__ . '/data/esc_sql.php');
        yield from self::gatherAssertTypes(__DIR__ . '/data/shortcode_atts.php');
        yield from self::gatherAssertTypes(__DIR__ . '/data/wp_die.php');
        yield from self::gatherAssertTypes(__DIR__ . '/data/wp_parse_url.php');
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
