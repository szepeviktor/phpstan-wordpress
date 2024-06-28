<?php

/**
 * Set return type of get_approved_comments() based on its passed arguments.
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use WP_Comment;

class GetApprovedCommentsDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return $functionReflection->getName() === 'get_approved_comments';
    }

    /**
     * - Return `WP_Comment[]` by default.
     * - Return `int[]` if `$fields = 'ids'`.
     * - Return `int` if `$count = true`.
     *
     * @see https://developer.wordpress.org/reference/functions/get_approved_comments/#parameters
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): ?Type
    {
        $args = $functionCall->getArgs();

        if (\count($args) < 2) {
            return self::defaultType();
        }

        $argumentType = $scope->getType($args[1]->value);
        if ($argumentType->isConstantArray()->no()) {
            return self::getIndeterminedType();
        }

        foreach ($argumentType->getConstantArrays() as $array) {
            if ($array->hasOffsetValueType(new ConstantStringType('count'))->yes()) {
                $fieldsValueTypes = $array->getOffsetValueType(new ConstantStringType('count'));
                if ($fieldsValueTypes->isTrue()->yes()) {
                    return new IntegerType();
                }
            }
            if (! $array->hasOffsetValueType(new ConstantStringType('fields'))->yes()) {
                continue;
            }

            $fieldsValueTypes = $array->getOffsetValueType(new ConstantStringType('fields'))->getConstantStrings();
            if (\count($fieldsValueTypes) === 0) {
                return self::getIndeterminedType();
            }
            if ($fieldsValueTypes[0]->getValue() === 'ids') {
                return new ArrayType(new IntegerType(), new IntegerType());
            }
        }

        return self::defaultType();
    }

    protected static function defaultType(): Type
    {
        return new ArrayType(new IntegerType(), new ObjectType(WP_Comment::class));
    }

    /**
     * Type defined on the PHPDocs.
     *
     * @return \PHPStan\Type\Type
     */
    protected static function getIndeterminedType(): Type
    {
        return TypeCombinator::union(
            new ArrayType(new IntegerType(), new ObjectType(WP_Comment::class)),
            new ArrayType(new IntegerType(), new IntegerType()),
            new IntegerType()
        );
    }
}
