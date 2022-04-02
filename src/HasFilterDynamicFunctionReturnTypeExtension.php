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
use PHPStan\Type\MixedType;
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

        if (($callbackArgumentType instanceof ConstantBooleanType) && ($callbackArgumentType->getValue() === false)) {
            return new BooleanType();
        }

        if ($callbackArgumentType instanceof MixedType) {
            return TypeCombinator::union(
                new BooleanType(),
                new IntegerType()
            );
        }

        return TypeCombinator::union(
            new ConstantBooleanType(false),
            new IntegerType()
        );
    }
}
