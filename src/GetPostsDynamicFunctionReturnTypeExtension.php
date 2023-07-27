<?php

/**
 * Set return type of get_posts().
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use WP_Post;
use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\Type;
use PHPStan\Type\ArrayType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\Constant\ConstantStringType;

use function count;

class GetPostsDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return $functionReflection->getName() === 'get_posts';
    }

    /**
     * @see https://developer.wordpress.org/reference/classes/wp_query/#return-fields-parameter
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): Type
    {
        $args = $functionCall->getArgs();

        // Called without arguments
        if (count($args) === 0) {
            return self::getObjectType();
        }

        $argumentType = $scope->getType($args[0]->value);
        $returnTypes = [];

        if ($argumentType->isConstantArray()->no()) {
            return self::getIndeterminedType();
        }

        if ($argumentType->isConstantArray()->maybe()) {
            $returnTypes[] = self::getIndeterminedType();
        }

        foreach ($argumentType->getConstantArrays() as $array) {
            if ($array->hasOffsetValueType(new ConstantStringType('fields'))->no()) {
                $returnTypes[] = self::getObjectType();
                continue;
            }

            if ($array->hasOffsetValueType(new ConstantStringType('fields'))->maybe()) {
                $returnTypes[] = self::getIndeterminedType();
                continue;
            }

            $fieldsValueTypes = $array->getOffsetValueType(new ConstantStringType('fields'))->getConstantStrings();

            if (count($fieldsValueTypes) === 0) {
                $returnTypes[] = self::getIndeterminedType();
                continue;
            }

            foreach ($fieldsValueTypes as $fieldsValueType) {
                switch ($fieldsValueType->getValue()) {
                    case 'id=>parent':
                    case 'ids':
                        $returnTypes[] = self::getIntType();
                        break;
                    default:
                        $returnTypes[] = self::getObjectType();
                }
            }
        }
        return TypeCombinator::union(...$returnTypes);
    }

    private static function getIntType(): Type
    {
        return new ArrayType(new IntegerType(), new IntegerType());
    }

    private static function getObjectType(): Type
    {
        return new ArrayType(new IntegerType(), new ObjectType(WP_Post::class));
    }

    private static function getIndeterminedType(): Type
    {
        return TypeCombinator::union(
            new ArrayType(new IntegerType(), new ObjectType(WP_Post::class)),
            new ArrayType(new IntegerType(), new IntegerType())
        );
    }
}
