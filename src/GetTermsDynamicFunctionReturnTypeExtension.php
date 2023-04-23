<?php

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
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\Accessory\AccessoryNumericStringType;
use PHPStan\Type\Constant\ConstantStringType;

class GetTermsDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    private const SUPPORTED_FUNCTIONS = [
        'get_tags' => 0,
        'get_terms' => 0,
        'wp_get_object_terms' => 2,
        'wp_get_post_categories' => 1,
        'wp_get_post_tags' => 1,
        'wp_get_post_terms' => 2,
    ];

    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return array_key_exists($functionReflection->getName(), self::SUPPORTED_FUNCTIONS);
    }

    /**
     * @see https://developer.wordpress.org/reference/functions/get_terms/
     * @see https://developer.wordpress.org/reference/classes/wp_term_query/__construct/
     *
     * phpcs:ignore NeutronStandard.Functions.LongFunction.LongFunction
     */
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): Type
    {
        $name = $functionReflection->getName();
        $argsParameterPosition = self::SUPPORTED_FUNCTIONS[$name] ?? null;

        if ($argsParameterPosition === null) {
            throw new \PHPStan\ShouldNotHappenException(
                sprintf('Could not detect parameter position for function %s()', $name)
            );
        }

        // Called without arguments
        if (! isset($functionCall->getArgs()[$argsParameterPosition])) {
            return self::termsType();
        }

        $argument = $scope->getType($functionCall->getArgs()[$argsParameterPosition]->value);
        $returnTypes = [];

        if ($argument->isConstantArray()->no()) {
            return self::defaultType();
        }
        if ($argument->isConstantArray()->maybe()) {
            $returnTypes[] = self::defaultType();
        }

        foreach ($argument->getConstantArrays() as $args) {
            if ($args->hasOffsetValueType(new ConstantStringType('fields'))->no()) {
                $returnTypes[] = self::termsType();
                continue;
            }
            if ($args->hasOffsetValueType(new ConstantStringType('fields'))->maybe()) {
                $returnTypes[] = self::defaultType();
                continue;
            }

            $fieldsValueTypes = $args->getOffsetValueType(new ConstantStringType('fields'))->getConstantStrings();
            if (count($fieldsValueTypes) === 0) {
                $returnTypes[] = self::defaultType();
                continue;
            }
            foreach ($fieldsValueTypes as $fieldsValueType) {
                $returnTypes[] = self::deduceTypeFromFields($fieldsValueType->getValue());
            }
        }
        return TypeCombinator::union(...$returnTypes);
    }

    protected static function deduceTypeFromFields(string $fields): Type
    {
        switch ($fields) {
            case 'count':
                return self::countType();
            case 'names':
            case 'slugs':
            case 'id=>name':
            case 'id=>slug':
                return self::slugsType();
            case 'ids':
            case 'tt_ids':
                return self::idsType();
            case 'id=>parent':
                return self::parentsType();
            case 'all':
            case 'all_with_object_id':
            default:
                return self::termsType();
        }
    }

    protected static function countType(): Type
    {
        return TypeCombinator::union(
            new StringType(),
            new ObjectType('WP_Error')
        );
    }

    protected static function slugsType(): Type
    {
        return TypeCombinator::union(
            new ArrayType(new IntegerType(), new StringType()),
            new ObjectType('WP_Error')
        );
    }

    protected static function idsType(): Type
    {
        return TypeCombinator::union(
            new ArrayType(new IntegerType(), new IntegerType()),
            new ObjectType('WP_Error')
        );
    }

    protected static function parentsType(): Type
    {
        return TypeCombinator::union(
            new ArrayType(new IntegerType(), new AccessoryNumericStringType()),
            new ObjectType('WP_Error')
        );
    }

    protected static function termsType(): Type
    {
        return TypeCombinator::union(
            new ArrayType(new IntegerType(), new ObjectType('WP_Term')),
            new ObjectType('WP_Error')
        );
    }

    protected static function defaultType(): Type
    {
        return TypeCombinator::union(
            self::termsType(),
            self::idsType(),
            self::slugsType(),
            self::countType()
        );
    }
}
