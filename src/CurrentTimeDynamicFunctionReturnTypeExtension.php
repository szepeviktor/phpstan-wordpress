<?php

/**
 * Set return type of current_time().
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

class CurrentTimeDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return $functionReflection->getName() === 'current_time';
    }

    /**
     * @see https://developer.wordpress.org/reference/functions/current_time/
     */
    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): ?Type
    {
        $argumentType = $scope->getType($functionCall->getArgs()[0]->value);

        // When called with a $type that isn't a constant string, return default return type
        if (count($argumentType->getConstantStrings()) === 0) {
            return null;
        }

        // Called with a constant string $type
        $returnType = [];
        foreach ($argumentType->getConstantStrings() as $constantString) {
            switch ($constantString->getValue()) {
                case 'timestamp':
                case 'U':
                    $returnType[] = new IntegerType();
                    break;
                case 'mysql':
                default:
                    $returnType[] = new StringType();
            }
        }

        return TypeCombinator::union(...$returnType);
    }
}
