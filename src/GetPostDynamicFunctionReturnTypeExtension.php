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
use PHPStan\Type\ArrayType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\MixedType;
use PHPStan\Type\NullType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;

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

        $objectType = TypeCombinator::union(
            new ObjectType('WP_Post'),
            new NullType()
        );
        $associativeArrayType = TypeCombinator::union(
            new ArrayType(new StringType(), new MixedType()),
            new NullType()
        );
        $numericArrayType = TypeCombinator::union(
            new ArrayType(new IntegerType(), new MixedType()),
            new NullType()
        );

        if (count($args) >= 2) {
            // When called with an $output that isn't a constant string, return default return type
            if (! $scope->getType($args[1]->value) instanceof ConstantStringType) {
                return TypeCombinator::union(
                    $objectType,
                    $associativeArrayType,
                    $numericArrayType
                );
            }
            if ($args[1]->value instanceof ConstFetch) {
                $output = $args[1]->value->name->getLast();
            }
        }

        switch ($output) {
            case 'ARRAY_A':
                $returnType = $associativeArrayType;
                break;
            case 'ARRAY_N':
                $returnType = $numericArrayType;
                break;
            default:
                $returnType = $objectType;
        }

        if ($functionReflection->getName() !== 'get_post') {
            return $returnType;
        }

        $firstArgType = count($args) > 0 ? $scope->getType($args[0]->value) : new NullType();
        if ($firstArgType instanceof ObjectType && $firstArgType->isInstanceOf('WP_Post')->yes()) {
            $returnType = TypeCombinator::remove($returnType, new NullType());
        }

        return $returnType;
    }
}
