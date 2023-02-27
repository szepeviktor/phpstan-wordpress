<?php

/**
 * Set return type of esc_sql().
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\Constant\ConstantArrayTypeBuilder;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\IntersectionType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\TypeTraverser;
use PHPStan\Type\UnionType;

class StringOrArrayDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return $functionReflection->getName() === 'esc_sql';
    }

    /**
     * @see https://developer.wordpress.org/reference/functions/esc_sql/
     */
    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): ?Type
    {
        if (count($functionCall->getArgs()) === 0) {
            return null;
        }

        return TypeTraverser::map(
            $scope->getType($functionCall->getArgs()[0]->value),
            function (Type $type, callable $traverse): Type {
                if ($type instanceof UnionType || $type instanceof IntersectionType) {
                    // phpcs:ignore NeutronStandard.Functions.VariableFunctions.VariableFunction
                    return $traverse($type);
                }

                return $this->getType($type);
            }
        );
    }

    private function getType(Type $type): Type
    {
        if ($type->isScalar()->yes()) {
            return new StringType();
        }

        if (!$type->isArray()->yes()) {
            return new ConstantStringType('');
        }

        if (count($type->getConstantArrays()) > 0) {
            return TypeCombinator::union(
                ...array_map(
                    function (ConstantArrayType $constantArray): Type {
                        $builder = ConstantArrayTypeBuilder::createEmpty();
                        foreach ($constantArray->getKeyTypes() as $i => $keyType) {
                            $builder->setOffsetValueType(
                                $keyType,
                                $this->getType($constantArray->getValueTypes()[$i]),
                                $constantArray->isOptionalKey($i)
                            );
                        }

                        return $builder->getArray();
                    },
                    $type->getConstantArrays()
                )
            );
        }

        return TypeCombinator::union(
            ...array_map(
                function (ArrayType $arrayType): Type {
                    return $arrayType->setOffsetValueType(
                        $arrayType->getIterableKeyType(),
                        $this->getType($arrayType->getIterableValueType())
                    );
                },
                $type->getArrays()
            )
        );
    }
}
