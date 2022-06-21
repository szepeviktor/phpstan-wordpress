<?php

// phpcs:disable SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter

/**
 * Set return type of term_exists().
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\Type;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\Constant\ConstantIntegerType;
use PHPStan\Type\StringType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\MixedType;
use PHPStan\Type\NullType;
use PHPStan\Type\TypeCombinator;

class TermExistsDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return in_array(
            $functionReflection->getName(),
            [
                'is_term',
                'tag_exists',
                'term_exists',
            ],
            true
        );
    }

    /**
     * @see https://developer.wordpress.org/reference/functions/term_exists/
     */
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): Type
    {
        $args = $functionCall->getArgs();

        if (count($args) === 0) {
            return new MixedType();
        }

        $termType = $scope->getType($args[0]->value);
        $taxonomyType = isset($args[1]) ? $scope->getType($args[1]->value) : new ConstantStringType('');

        if ($termType instanceof NullType) {
            return new NullType();
        }

        if (($termType instanceof ConstantIntegerType) && $termType->getValue() === 0) {
            return new ConstantIntegerType(0);
        }

        $withTaxonomy = new ConstantArrayType(
            [
                new ConstantStringType('term_id'),
                new ConstantStringType('term_taxonomy_id'),
            ],
            [
                new StringType(),
                new StringType(),
            ]
        );
        $withoutTaxonomy = new StringType();

        if (($taxonomyType instanceof ConstantStringType) && $taxonomyType->getValue() !== '') {
            return TypeCombinator::union(
                $withTaxonomy,
                new NullType()
            );
        }

        if (($taxonomyType instanceof ConstantStringType) && $taxonomyType->getValue() === '') {
            return TypeCombinator::union(
                $withoutTaxonomy,
                new NullType()
            );
        }

        return TypeCombinator::union(
            $withTaxonomy,
            $withoutTaxonomy,
            new NullType()
        );
    }
}
