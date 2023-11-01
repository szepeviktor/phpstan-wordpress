<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use SzepeViktor\PHPStan\WordPress\WpThemeMagicPropertiesClassReflectionExtension;

class WpThemeMagicPropertiesClassReflectionExtensionTest extends \PHPStan\Testing\PHPStanTestCase
{
    /** @var \PHPStan\Broker\Broker */
    private $broker;

    protected function setUp(): void
    {
        $this->broker = $this->createBroker();
    }

    /**
     * @return list<array{string, string, bool}>
     */
    public function dataHasProperty(): array
    {
        return [
            ['WP_Theme', 'foo', false],
            ['WP_Theme', 'author', true],
            ['WP_Theme', 'description', true],
            ['WP_Theme', 'name', true],
            ['WP_Theme', 'parent_theme', true],
            ['WP_Theme', 'screenshot', true],
            ['WP_Theme', 'stylesheet', true],
            ['WP_Theme', 'stylesheet_dir', true],
            ['WP_Theme', 'tags', true],
            ['WP_Theme', 'template', true],
            ['WP_Theme', 'template_dir', true],
            ['WP_Theme', 'theme_root', true],
            ['WP_Theme', 'theme_root_uri', true],
            ['WP_Theme', 'title', true],
            ['WP_Theme', 'version', true],
        ];
    }

    /**
     * @dataProvider dataHasProperty
     */
    public function testHasProperty(string $className, string $property, bool $expected): void
    {
        $classReflection = $this->broker->getClass($className);
        $extension = new WpThemeMagicPropertiesClassReflectionExtension();
        self::assertSame($expected, $extension->hasProperty($classReflection, $property));
    }
}
