<?php

/**
 * Set return type of _get_list_table().
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\Type;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\Constant\ConstantStringType;

class GetListTableDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return $functionReflection->getName() === '_get_list_table';
    }

    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): Type
    {
        $args = $functionCall->getArgs();

        // Called without $class argument
        if (count($args) < 1) {
            return new ConstantBooleanType(false);
        }

        $argumentType = $scope->getType($args[0]->value);

        // When called with a $class that isn't a constant string, return default return type
        if (! $argumentType instanceof ConstantStringType) {
            return ParametersAcceptorSelector::selectFromArgs(
                $scope,
                $args,
                $functionReflection->getVariants()
            )->getReturnType();
        }

        return TypeCombinator::union(
            new ObjectType($argumentType->getValue()),
            new ConstantBooleanType(false)
        );
    }
}
