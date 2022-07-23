<?php

// phpcs:disable SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter

/**
 * Set return type of WP_Theme::get().
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\ArrayType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;

class WpThemeGetDynamicMethodReturnTypeExtension implements \PHPStan\Type\DynamicMethodReturnTypeExtension
{
    /**
     * File headers.
     *
     * @var array<string,string>
     */
    private static $headers = [
        'Name' => 'Theme Name',
        'ThemeURI' => 'Theme URI',
        'Description' => 'Description',
        'Author' => 'Author',
        'AuthorURI' => 'Author URI',
        'Version' => 'Version',
        'Template' => 'Template',
        'Status' => 'Status',
        'Tags' => 'Tags',
        'TextDomain' => 'Text Domain',
        'DomainPath' => 'Domain Path',
        'RequiresWP' => 'Requires at least',
        'RequiresPHP' => 'Requires PHP',
    ];

    public function getClass(): string
    {
        return '\WP_Theme';
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'get';
    }

    /**
     * @see https://developer.wordpress.org/reference/classes/wp_theme/get/
     */
    public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): Type
    {
        $argumentType = $scope->getType($methodCall->getArgs()[0]->value);

        if (!$argumentType instanceof ConstantStringType) {
            return new ConstantBooleanType(false);
        }

        if ($argumentType->getValue() === 'Tags') {
            return new ArrayType(new IntegerType(), new StringType());
        }

        if (isset(self::$headers[$argumentType->getValue()])) {
            return new StringType();
        }

        return new ConstantBooleanType(false);
    }
}
