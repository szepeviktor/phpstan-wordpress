<?php

/**
 * Set return type of WP_Theme::get().
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\ArrayType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use WP_Theme;

class WpThemeGetDynamicMethodReturnTypeExtension implements \PHPStan\Type\DynamicMethodReturnTypeExtension
{
    /**
     * File headers.
     *
     * @var list<string>
     */
    protected static $headers = [
        'Name',
        'ThemeURI',
        'Description',
        'Author',
        'AuthorURI',
        'Version',
        'Template',
        'Status',
        'Tags',
        'TextDomain',
        'DomainPath',
        'RequiresWP',
        'RequiresPHP',
    ];

    public function getClass(): string
    {
        return WP_Theme::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'get';
    }

    /**
     * @see https://developer.wordpress.org/reference/classes/wp_theme/get/
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): Type
    {
        $argumentType = $scope->getType($methodCall->getArgs()[0]->value);

        if (count($argumentType->getConstantStrings()) === 0) {
            return TypeCombinator::union(
                new StringType(),
                new ArrayType(new IntegerType(), new StringType()),
                new ConstantBooleanType(false)
            );
        }

        $returnType = [];
        foreach ($argumentType->getConstantStrings() as $constantString) {
            if ($constantString->getValue() === 'Tags') {
                $returnType[] = new ArrayType(new IntegerType(), new StringType());
            } elseif (in_array($constantString->getValue(), self::$headers, true)) {
                $returnType[] = new StringType();
            } else {
                $returnType[] = new ConstantBooleanType(false);
            }
        }

        return TypeCombinator::union(...$returnType);
    }
}
