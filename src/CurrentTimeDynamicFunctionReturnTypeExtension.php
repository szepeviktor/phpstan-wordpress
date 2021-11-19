<?php

/**
 * Set return type of current_time().
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\Type;
use PHPStan\Type\IntegerType;
use PHPStan\Type\StringType;
use PHPStan\Type\Constant\ConstantStringType;

class CurrentTimeDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return in_array($functionReflection->getName(), ['current_time'], true);
    }

    /**
     * @see https://developer.wordpress.org/reference/functions/current_time/
     */
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): Type
    {
        $args = $functionCall->getArgs();
        $argumentType = $scope->getType($args[0]->value);

        // When called with a $type that isn't a constant string, return default return type
        if (! $argumentType instanceof ConstantStringType) {
            return ParametersAcceptorSelector::selectFromArgs(
                $scope,
                $args,
                $functionReflection->getVariants()
            )->getReturnType();
        }

        // Called with a constant string $type
        switch ($argumentType->getValue()) {
            case 'timestamp':
            case 'U':
                return new IntegerType();
            case 'mysql':
            default:
                return new StringType();
        }
    }
}
