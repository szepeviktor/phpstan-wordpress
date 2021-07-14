<?php

/**
 * Accept magic properties of WP_Theme.
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\Reflection\Dummy\DummyPropertyReflection;

class WpThemeMagicPropertiesClassReflectionExtension implements \PHPStan\Reflection\PropertiesClassReflectionExtension
{
    /** @var array<int, string> */
    private $properties = [ // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
        'name', 'title', 'version', 'parent_theme', 'template_dir', 'stylesheet_dir', 'template', 'stylesheet',
        'screenshot', 'description', 'author', 'tags', 'theme_root', 'theme_root_uri',
    ];

    public function hasProperty(ClassReflection $classReflection, string $propertyName): bool
    {
        if ($classReflection->getName() !== 'WP_Theme') {
            return false;
        }
        return in_array($propertyName, $this->properties, true);
    }

    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
    public function getProperty(ClassReflection $classReflection, string $propertyName): PropertyReflection
    {
        // @phpstan-ignore-next-line This is not covered by BC.
        return new DummyPropertyReflection();
    }
}
