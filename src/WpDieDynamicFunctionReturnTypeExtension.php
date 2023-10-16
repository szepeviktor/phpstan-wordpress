<?php

/**
 * Set return type of wp_die().
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\Type;
use PHPStan\Type\VoidType;
use PHPStan\Type\NonAcceptingNeverType;

class WpDieDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return $functionReflection->getName() === 'wp_die';
    }

    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): Type
    {
        $args = $functionCall->getArgs();

        // Called without $args parameter
        if (count($args) < 3) {
            return new NonAcceptingNeverType();
        }

        $argType = $scope->getType($args[2]->value);

        // Return void for non constant arrays.
        if (! $argType->isConstantArray()->yes()) {
            return new VoidType();
        }

        // Return never if the key 'exit' is not set.
        if (! $argType->hasOffsetValueType(new ConstantStringType('exit'))->yes()) {
            return new NonAcceptingNeverType();
        }

        // Note WP's wp_die handlers do lazy comparison
        return $argType->getOffsetValueType(new ConstantStringType('exit'))->toBoolean()->isTrue()->yes()
            ? new NonAcceptingNeverType()
            : new VoidType();
    }
}
