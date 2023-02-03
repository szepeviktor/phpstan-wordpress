<?php

/**
 * Set return type of get_permalink() and get_the_permalink().
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;

class GetPermalinkDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return in_array(
            $functionReflection->getName(),
            [
                'get_permalink',
                'get_the_permalink',
            ],
            true
        );
    }

    /**
     * @see https://developer.wordpress.org/reference/functions/get_permalink/
     * @see https://developer.wordpress.org/reference/functions/get_the_permalink/
     */
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): Type
    {
        $args = $functionCall->getArgs();

        if (count($args) > 0) {
            $type = $scope->getType($args[0]->value);

            // Called with a WP_Post instance
            if ($type instanceof ObjectType && $type->isInstanceOf('WP_Post')->yes()) {
                return new StringType();
            }
        }

        // When called without arguments or with a $type that isn't a WP_Post instance, return default return type
        return ParametersAcceptorSelector::selectFromArgs(
            $scope,
            $args,
            $functionReflection->getVariants()
        )->getReturnType();
    }
}
