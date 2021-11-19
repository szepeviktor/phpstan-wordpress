<?php

/**
 * Set return type of get_post().
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\Type;
use PHPStan\Type\ArrayType;
use PHPStan\Type\StringType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\NullType;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\Constant\ConstantStringType;

class GetPostDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return in_array($functionReflection->getName(), ['get_post', 'get_page_by_path'], true);
    }

    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): Type
    {
        $output = 'OBJECT';
        $args = $functionCall->getArgs();

        if (count($functionCall->args) >= 2) {
            $argumentType = $scope->getType($args[1]->value);

            // When called with an $output that isn't a constant string, return default return type
            if (! $argumentType instanceof ConstantStringType) {
                return ParametersAcceptorSelector::selectFromArgs(
                    $scope,
                    $args,
                    $functionReflection->getVariants()
                )->getReturnType();
            }
        }

        if (count($args) >= 2 && $args[1]->value instanceof ConstFetch) {
            $output = $args[1]->value->name->getLast();
        }
        if ($output === 'ARRAY_A') {
            return TypeCombinator::union(new ArrayType(new StringType(), new MixedType()), new NullType());
        }
        if ($output === 'ARRAY_N') {
            return TypeCombinator::union(new ArrayType(new IntegerType(), new MixedType()), new NullType());
        }

        return TypeCombinator::union(new ObjectType('WP_Post'), new NullType());
    }
}
