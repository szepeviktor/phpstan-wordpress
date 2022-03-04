<?php

//phpcs:disable SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter

/**
 * Set return type of get_terms() and related functions.
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
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\ConstantScalarType;
use PHPStan\Type\TypeCombinator;

class GetTermsDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return in_array($functionReflection->getName(), [
            'get_tags',
            'get_terms',
            'wp_get_object_terms',
        ], true);
    }

    /**
     * @see https://developer.wordpress.org/reference/functions/get_terms/
     * @see https://developer.wordpress.org/reference/classes/wp_term_query/__construct/
     */
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): Type
    {
        $slugsType = new ArrayType(new IntegerType(), new StringType());
        $idsType = new ArrayType(new IntegerType(), new IntegerType());
        $parentsType = new ArrayType(new IntegerType(), new StringType());
        $termsType = new ArrayType(new IntegerType(), new ObjectType('WP_Term'));
        $countType = new StringType();
        $errorType = new ObjectType('WP_Error');

        // Called without arguments
        if (count($functionCall->args) === 0) {
            return TypeCombinator::union(
                $termsType,
                $errorType
            );
        }

        $argumentType = $scope->getType($functionCall->args[0]->value);
        $args = [
            'fields' => 'all',
            'count' => false,
        ];

        if ($argumentType instanceof ConstantArrayType) {
            // Called with an array argument
            foreach ($argumentType->getKeyTypes() as $index => $key) {
                if (! $key instanceof ConstantStringType) {
                    return TypeCombinator::union(
                        $termsType,
                        $idsType,
                        $slugsType,
                        $countType,
                        $errorType
                    );
                }

                unset($args[$key->getValue()]);
                $fieldsType = $argumentType->getValueTypes()[$index];
                if ($fieldsType instanceof ConstantScalarType) {
                    $args[$key->getValue()] = $fieldsType->getValue();
                }
            }
        } else {
            // Without constant array argument return default return type
            return TypeCombinator::union(
                $termsType,
                $idsType,
                $slugsType,
                $countType,
                $errorType
            );
        }

        if (isset($args['count']) && true === $args['count']) {
            return TypeCombinator::union(
                $countType,
                $errorType
            );
        }

        if (! isset($args['fields'], $args['count'])) {
            return TypeCombinator::union(
                $termsType,
                $idsType,
                $slugsType,
                $countType,
                $errorType
            );
        }

        switch ($args['fields']) {
            case 'count':
                return TypeCombinator::union(
                    $countType,
                    $errorType
                );
            case 'names':
            case 'slugs':
            case 'id=>name':
            case 'id=>slug':
                return TypeCombinator::union(
                    $slugsType,
                    $errorType
                );
            case 'ids':
            case 'tt_ids':
                return TypeCombinator::union(
                    $idsType,
                    $errorType
                );
            case 'id=>parent':
                return TypeCombinator::union(
                    $parentsType,
                    $errorType
                );
            case 'all':
            case 'all_with_object_id':
            default:
                return TypeCombinator::union(
                    $termsType,
                    $errorType
                );
        }
    }
}
