<?php

/**
 * Set return type of shortcode_atts().
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;

final class ShortcodeAttsDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return $functionReflection->getName() === 'shortcode_atts';
    }

    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): Type
    {
        if ($functionCall->args === []) {
            return ParametersAcceptorSelector::selectFromArgs(
                $scope,
                $functionCall->args,
                $functionReflection->getVariants()
            )->getReturnType();
        }

        $type = $scope->getType($functionCall->args[0]->value);

        if ($type instanceof ConstantArrayType) {
            // shortcode_atts values are coming from the defined defaults or from the actual string shortcode attributes
            return new ConstantArrayType(
                $type->getKeyTypes(),
                array_map(
                    static function (Type $valueType): Type {
                        return TypeCombinator::union($valueType, new StringType());
                    },
                    $type->getValueTypes()
                )
            );
        }

        return $type;
    }
}
