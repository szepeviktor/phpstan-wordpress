<?php

/**
 * Set return type of mysql2date().
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
use PHPStan\Type\UnionType;
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\Constant\ConstantStringType;

class MySQL2DateDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return in_array($functionReflection->getName(), ['mysql2date'], true);
    }

    /**
     * @see https://developer.wordpress.org/reference/functions/mysql2date/
     */
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): Type
    {
        $args = $functionCall->getArgs();
        $argumentType = $scope->getType($args[0]->value);

        // When called with a $format that isn't a constant string, return default return type
        if (! $argumentType instanceof ConstantStringType) {
            return ParametersAcceptorSelector::selectFromArgs(
                $scope,
                $args,
                $functionReflection->getVariants()
            )->getReturnType();
        }

        // Called with a constant string $format
        switch ($argumentType->getValue()) {
            case 'G':
            case 'U':
                return new UnionType([new ConstantBooleanType(false), new IntegerType()]);
            default:
                return new UnionType([new ConstantBooleanType(false), new StringType()]);
        }
    }
}
