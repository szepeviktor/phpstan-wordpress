<?php

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
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): Type
    {
        $args = $functionCall->getArgs();

        if (count($args) === 0) {
            return new MixedType();
        }

        $termType = $scope->getType($args[0]->value);
        $taxonomyType = isset($args[1]) ? $scope->getType($args[1]->value) : new ConstantStringType('');

        $returnType = [new NullType()];

        if ($termType->isNull()->yes()) {
            return new NullType();
        }

        if (($termType instanceof ConstantIntegerType)) {
            if ($termType->getValue() === 0) {
                return new ConstantIntegerType(0);
            }
        } elseif ($termType->isInteger()->no() === false) {
            $returnType[] = new ConstantIntegerType(0);
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

        if (count($taxonomyType->getConstantStrings()) === 0) {
            $returnType[] = $withTaxonomy;
            $returnType[] = $withoutTaxonomy;
            return TypeCombinator::union(...$returnType);
        }

        foreach ($taxonomyType->getConstantStrings() as $constantString) {
            $returnType[] = $constantString->getValue() === ''
                ? $withoutTaxonomy
                : $withTaxonomy;
        }
        return TypeCombinator::union(...$returnType);
    }
}
