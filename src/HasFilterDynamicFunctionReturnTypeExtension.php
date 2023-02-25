<?php

/**
 * Set return type of has_filter() and has_action().
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\Type;
use PHPStan\Type\BooleanType;
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\TypeCombinator;

class HasFilterDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return in_array($functionReflection->getName(), ['has_filter', 'has_action'], true);
    }

    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): Type
    {
        $args = $functionCall->getArgs();
        $callbackArgumentType = new ConstantBooleanType(false);

        if (isset($args[1])) {
            $callbackArgumentType = $scope->getType($args[1]->value);
        }

        if ($callbackArgumentType->isFalse()->yes()) {
            return new BooleanType();
        }

        $returnType = [new ConstantBooleanType(false), new IntegerType()];
        if ($callbackArgumentType->isFalse()->maybe()) {
            $returnType[] = new BooleanType();
        }

        return TypeCombinator::union(...$returnType);
    }
}
