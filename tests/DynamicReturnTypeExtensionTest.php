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
        // path to a file with actual asserts of expected types:
        yield from $this->gatherAssertTypes(__DIR__ . '/data/_get_list_table.php');
        yield from $this->gatherAssertTypes(__DIR__ . '/data/apply_filters.php');
        yield from $this->gatherAssertTypes(__DIR__ . '/data/current_time.php');
        yield from $this->gatherAssertTypes(__DIR__ . '/data/get_comment.php');
        yield from $this->gatherAssertTypes(__DIR__ . '/data/get_object_taxonomies.php');
        yield from $this->gatherAssertTypes(__DIR__ . '/data/get_post.php');
        yield from $this->gatherAssertTypes(__DIR__ . '/data/mysql2date.php');
        yield from $this->gatherAssertTypes(__DIR__ . '/data/shortcode_atts.php');
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
        // path to your project's phpstan.neon, or extension.neon in case of custom extension packages
        return [__DIR__ . '/../extension.neon'];
    }
}
