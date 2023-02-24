<?php

/**
 * Set return type of mysql2date().
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\Type;
use PHPStan\Type\IntegerType;
use PHPStan\Type\StringType;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\UnionType;
use PHPStan\Type\Constant\ConstantBooleanType;

class MySQL2DateDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return $functionReflection->getName() === 'mysql2date';
    }

    /**
     * @see https://developer.wordpress.org/reference/functions/mysql2date/
     */
    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): ?Type
    {
        $argumentType = $scope->getType($functionCall->getArgs()[0]->value);

        // When called with a $format that isn't a constant string, return default return type
        if (count($argumentType->getConstantStrings()) === 0) {
            return null;
        }

        // Called with a constant string $format
        $returnType = [];
        foreach ($argumentType->getConstantStrings() as $constantString) {
            switch ($constantString->getValue()) {
                case 'G':
                case 'U':
                    $returnType[] = new UnionType([new ConstantBooleanType(false), new IntegerType()]);
                    break;
                default:
                    $returnType[] = new UnionType([new ConstantBooleanType(false), new StringType()]);
            }
        }

        return TypeCombinator::union(...$returnType);
    }
}
