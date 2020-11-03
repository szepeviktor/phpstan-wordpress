<?php

/**
 * Accept magic properties of wpdb.
 */

declare(strict_types=1);

namespace PHPStan\WordPress;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\Reflection\Dummy\DummyPropertyReflection;

class wpdbMagicPropertiesClassReflectionExtension implements \PHPStan\Reflection\PropertiesClassReflectionExtension
{
	/** @var array<int, string> */
	private $properties = [ // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
	                        'result',
	                        'col_meta',
	                        'table_charset',
	                        'check_current_query',
	                        'checking_collation',
	                        'col_info',
	                        'reconnect_retries',
	                        'dbuser',
	                        'dbpassword',
	                        'dbname',
	                        'dbhost',
	                        'dbh',
	                        'incompatible_modes',
	                        'use_mysqli',
	                        'has_connected',
	];

	public function hasProperty(ClassReflection $classReflection, string $propertyName): bool
	{
		if ($classReflection->getName() !== 'wpdb') {
			return false;
		}
		return in_array($propertyName, $this->properties, true);
	}

	// phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
	public function getProperty(ClassReflection $classReflection, string $propertyName): PropertyReflection
	{
		return new DummyPropertyReflection();
	}
}
