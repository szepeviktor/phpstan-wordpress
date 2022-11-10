<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

class AssertMethodTypeSpecifyingExtensionTest extends \PHPStan\Testing\TypeInferenceTestCase
{
    /**
     * @return iterable<mixed>
     */
    public function dataFileAsserts(): iterable
    {
        // Path to a file with actual asserts of expected types:
        yield from $this->gatherAssertTypes(__DIR__ . '/data/assert_wp_error.php');
        yield from $this->gatherAssertTypes(__DIR__ . '/data/assert_not_wp_error.php');
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
