<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

/**
 * Type inference tests for code analysed as PHP 7.3.
 */
class DynamicReturnTypeExtensionPhp73Test extends \PHPStan\Testing\TypeInferenceTestCase
{
    /**
     * @return array<mixed>
     */
    public function dataFileAsserts(): array
    {
        return self::gatherAssertTypes(__DIR__ . '/data/wp-hash-password-php-7.3.php');
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
            __DIR__ . '/php-7.3.neon',
        ];
    }
}
