<?php

/**
 * Set return type of esc_sql().
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\Constant\ConstantArrayTypeBuilder;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\IntersectionType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeTraverser;
use PHPStan\Type\UnionType;

class EscSqlDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return $functionReflection->getName() === 'esc_sql';
    }

    /**
     * @see https://developer.wordpress.org/reference/functions/esc_sql/
     */
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): ?Type
    {
        if (count($functionCall->getArgs()) === 0) {
            return null;
        }

        $defaultReturnType = ParametersAcceptorSelector::selectFromArgs(
            $scope,
            $functionCall->getArgs(),
            $functionReflection->getVariants()
        )->getReturnType();

        return TypeTraverser::map(
            $scope->getType($functionCall->getArgs()[0]->value),
            function (Type $type, callable $traverse) use ($defaultReturnType): Type {
                if ($type instanceof UnionType || $type instanceof IntersectionType) {
                    // phpcs:ignore NeutronStandard.Functions.VariableFunctions.VariableFunction
                    return $traverse($type);
                }

                return $this->getType($type, $defaultReturnType);
            }
        );
    }

    private function getType(Type $type, Type $defaultReturnType): Type
    {
        if ($type->isScalar()->yes()) {
            return new StringType();
        }

        if ($type->isArray()->no()) {
            return new ConstantStringType('');
        }

        if (count($type->getConstantArrays()) > 0) {
            $constantArray = $type->getConstantArrays()[0]; // will only have one because of TypeTraverser::map()
            $builder = ConstantArrayTypeBuilder::createEmpty();
            foreach ($constantArray->getKeyTypes() as $i => $keyType) {
                $builder->setOffsetValueType(
                    $keyType,
                    $this->getType($constantArray->getValueTypes()[$i], $defaultReturnType),
                    $constantArray->isOptionalKey($i)
                );
            }

            return $builder->getArray();
        }

        if (count($type->getArrays()) > 0) {
            $arrayType = $type->getArrays()[0]; // will only have one because of TypeTraverser::map()
            return $arrayType->setOffsetValueType(
                $arrayType->getIterableKeyType(),
                $this->getType($arrayType->getIterableValueType(), $defaultReturnType)
            );
        }

        return $defaultReturnType;
    }
}
