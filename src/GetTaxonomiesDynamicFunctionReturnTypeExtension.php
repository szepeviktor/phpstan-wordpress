<?php

// phpcs:disable SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter

/**
 * Set return type of get_taxonomies().
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\Type;
use PHPStan\Type\ArrayType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\TypeCombinator;

class GetTaxonomiesDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return in_array($functionReflection->getName(), ['get_taxonomies'], true);
    }

    /**
     * @see https://developer.wordpress.org/reference/functions/get_taxonomies/
     */
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): Type
    {
        $objectsReturnType = new ArrayType(new StringType(), new ObjectType('WP_Taxonomy'));
        $namesReturnType = new ArrayType(new StringType(), new StringType());
        $indeterminateReturnType = TypeCombinator::union(
            $objectsReturnType,
            $namesReturnType
        );

        $args = $functionCall->getArgs();
        // Called without second $output arguments

        if (count($args) <= 1) {
            return $namesReturnType;
        }

        $argumentType = $scope->getType($args[1]->value);

        // When called with a non-string $output, return default return type
        if (! $argumentType instanceof ConstantStringType) {
            return $indeterminateReturnType;
        }

        // Called with a string $output
        switch ($argumentType->getValue()) {
            case 'objects':
                return $objectsReturnType;
            case 'names':
            default:
                return $namesReturnType;
        }
    }
}
