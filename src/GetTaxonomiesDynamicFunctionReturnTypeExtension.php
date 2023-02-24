<?php

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
use PHPStan\Type\TypeCombinator;

class GetTaxonomiesDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return $functionReflection->getName() === 'get_taxonomies';
    }

    /**
     * @see https://developer.wordpress.org/reference/functions/get_taxonomies/
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): Type
    {
        $objectsReturnType = new ArrayType(new StringType(), new ObjectType('WP_Taxonomy'));
        $namesReturnType = new ArrayType(new StringType(), new StringType());

        $args = $functionCall->getArgs();

        // Called without second $output arguments
        if (count($args) <= 1) {
            return $namesReturnType;
        }

        $argumentType = $scope->getType($args[1]->value);

        // When called with a non-string $output, return default return type
        if (count($argumentType->getConstantStrings()) === 0) {
            return TypeCombinator::union(
                $objectsReturnType,
                $namesReturnType
            );
        }

        // Called with a string $output
        $returnType = [];
        foreach ($argumentType->getConstantStrings() as $constantString) {
            switch ($constantString->getValue()) {
                case 'objects':
                    $returnType[] = $objectsReturnType;
                    break;
                case 'names':
                default:
                    $returnType[] = $namesReturnType;
            }
        }

        return TypeCombinator::union(...$returnType);
    }
}
