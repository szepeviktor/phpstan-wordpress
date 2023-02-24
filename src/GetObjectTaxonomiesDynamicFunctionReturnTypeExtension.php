<?php

/**
 * Set return type of get_object_taxonomies().
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\Type;
use PHPStan\Type\ArrayType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\TypeCombinator;

class GetObjectTaxonomiesDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return in_array($functionReflection->getName(), ['get_object_taxonomies'], true);
    }

    /**
     * @see https://developer.wordpress.org/reference/functions/get_object_taxonomies/
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): ?Type
    {
        $args = $functionCall->getArgs();

        // Called without second $output argument
        if (count($args) <= 1) {
            return new ArrayType(new IntegerType(), new StringType());
        }

        $argumentType = $scope->getType($args[1]->value);

        // When called with an $output that isn't a constant string, return default return type
        if (count($argumentType->getConstantStrings()) === 0) {
            return null;
        }

        // Called with a constant string $output
        $returnType = [];
        foreach ($argumentType->getConstantStrings() as $constantString) {
            switch ($constantString->getValue()) {
                case 'objects':
                    $returnType[] = new ArrayType(new StringType(), new ObjectType('WP_Taxonomy'));
                    break;
                case 'names':
                default:
                    $returnType[] = new ArrayType(new IntegerType(), new StringType());
            }
        }

        return TypeCombinator::union(...$returnType);
    }
}
