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
        yield from self::gatherAssertTypes(__DIR__ . '/data/apply-filters.php');
        yield from self::gatherAssertTypes(__DIR__ . '/data/ApplyFiltersTestClass.php');
        yield from self::gatherAssertTypes(__DIR__ . '/data/esc-sql.php');
        yield from self::gatherAssertTypes(__DIR__ . '/data/normalize-whitespace.php');
        yield from self::gatherAssertTypes(__DIR__ . '/data/shortcode-atts.php');
        yield from self::gatherAssertTypes(__DIR__ . '/data/stripslashes-from-strings-only.php');
        yield from self::gatherAssertTypes(__DIR__ . '/data/wp-parse-url.php');
        yield from self::gatherAssertTypes(__DIR__ . '/data/wp-slash.php');

        $phpstanVersion = self::getContainer()->getByType(InstalledPhpStanVersion::class);

        if ($phpstanVersion->satisfies('^2.1.18')) {
            // Improved rtrim handling in PHPStan 2.1.18 gives different results
            yield from self::gatherAssertTypes(__DIR__ . '/data/slashit-functions.php');
        }
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
        return [
            dirname(__DIR__) . '/vendor/szepeviktor/phpstan-wordpress/extension.neon',
            __DIR__ . '/test-services.neon',
        ];
    }
}
