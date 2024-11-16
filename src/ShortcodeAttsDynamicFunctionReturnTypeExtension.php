<?php

/**
 * Set return type of shortcode_atts().
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\Accessory\NonEmptyArrayType;
use PHPStan\Type\ArrayType;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\Constant\ConstantArrayTypeBuilder;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;

final class ShortcodeAttsDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return $functionReflection->getName() === 'shortcode_atts';
    }

    /**
     * @see https://developer.wordpress.org/reference/functions/shortcode_atts/
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): ?Type
    {
        $args = $functionCall->getArgs();

        if (count($args) < 2) {
            return null;
        }

        $pairsType = $scope->getType($args[0]->value);
        $attsType = $scope->getType($args[1]->value);

        if ($attsType->isIterableAtLeastOnce()->no()) {
            return $pairsType;
        }

        if (! $this->hasConstantArrays($pairsType)) {
            return $this->resolveTypeForNonConstantPairs($pairsType);
        }

        if (! $this->hasConstantArrays($attsType)) {
            return $this->resolveTypeForNonConstantAtts($pairsType);
        }

        return $this->resolveTypeForConstantAtts($pairsType, $attsType);
    }

    protected function resolveTypeForNonConstantPairs(Type $pairsType): Type
    {
        $keyType = $pairsType->getIterableKeyType();
        $valueType = TypeCombinator::union(
            $pairsType->getIterableValueType(),
            new StringType()
        );
        $arrayType = new ArrayType($keyType, $valueType);

        return $pairsType->isIterableAtLeastOnce()->yes()
            ? TypeCombinator::intersect($arrayType, new NonEmptyArrayType())
            : $arrayType;
    }

    protected function resolveTypeForNonConstantAtts(Type $pairsType): Type
    {
        $types = [];

        foreach ($pairsType->getConstantArrays() as $constantArray) {
            $types[] = new ConstantArrayType(
                $constantArray->getKeyTypes(),
                array_map(
                    static function (Type $valueType): Type {
                        return TypeCombinator::union($valueType, new StringType());
                    },
                    $constantArray->getValueTypes()
                )
            );
        }

        return TypeCombinator::union(...$types);
    }

    protected function resolveTypeForConstantAtts(Type $pairsType, Type $attsType): Type
    {
        $types = [];

        foreach ($pairsType->getConstantArrays() as $constantPairsArray) {
            foreach ($attsType->getConstantArrays() as $constantAttsArray) {
                $types[] = $this->mergeArrays($constantPairsArray, $constantAttsArray);
            }
        }

        return TypeCombinator::union(...$types);
    }

    protected function mergeArrays(ConstantArrayType $pairsArray, ConstantArrayType $attsArray): Type
    {
        if (count($attsArray->getKeyTypes()) === 0) {
            return $pairsArray;
        }

        $builder = ConstantArrayTypeBuilder::createFromConstantArray($pairsArray);

        foreach ($pairsArray->getKeyTypes() as $keyType) {
            $hasOffsetValueType = $attsArray->hasOffsetValueType($keyType);

            if ($hasOffsetValueType->no()) {
                continue;
            }

            $valueType = $hasOffsetValueType->yes()
                ? $attsArray->getOffsetValueType($keyType)
                : TypeCombinator::union(
                    $pairsArray->getOffsetValueType($keyType),
                    $attsArray->getOffsetValueType($keyType)
                );

            $builder->setOffsetValueType($keyType, $valueType);
        }

        return $builder->getArray();
    }

    protected function hasConstantArrays(Type $type): bool
    {
        return count($type->getConstantArrays()) > 0;
    }
}
