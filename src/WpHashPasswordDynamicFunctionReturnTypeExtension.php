<?php

/**
 * Set return type of wp_hash_password() based on the analysed PHP version.
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Php\PhpVersion;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\Accessory\AccessoryNonFalsyStringType;
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\IntersectionType;
use PHPStan\Type\NullType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;

final class WpHashPasswordDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    private PhpVersion $phpVersion;

    public function __construct(PhpVersion $phpVersion)
    {
        $this->phpVersion = $phpVersion;
    }

    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return $functionReflection->getName() === 'wp_hash_password';
    }

    /**
     * The hashing algorithm is filterable and not validated.
     * For an invalid algorithm or invalid options, password_hash()
     * throws on PHP 8, returns null on PHP 7, and may also return false before PHP 7.4.
     * All other code paths return a non-falsy string.
     *
     * With a configured PHP version range, PHPStan's PhpVersion is the minimum,
     * so the result is the union over the whole range.
     *
     * @see https://developer.wordpress.org/reference/functions/wp_hash_password/
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): Type
    {
        $types = [new IntersectionType([new StringType(), new AccessoryNonFalsyStringType()])];

        if ($this->phpVersion->getVersionId() < 80000) {
            $types[] = new NullType();
        }

        if ($this->phpVersion->getVersionId() < 70400) {
            $types[] = new ConstantBooleanType(false);
        }

        return TypeCombinator::union(...$types);
    }
}
