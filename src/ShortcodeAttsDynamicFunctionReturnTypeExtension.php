<?php

/**
 * Set return type of shortcode_atts().
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
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

    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): ?Type
    {
        $args = $functionCall->getArgs();
        if ($args === []) {
            return null;
        }

        $type = $scope->getType($args[0]->value);

        if (count($type->getConstantArrays()) === 0) {
            return $type;
        }

        $returnType = [];
        foreach ($type->getConstantArrays() as $constantArray) {
            // shortcode_atts values are coming from the defined defaults or from the actual string shortcode attributes
            $returnType[] = new ConstantArrayType(
                $constantArray->getKeyTypes(),
                array_map(
                    static function (Type $valueType): Type {
                        return TypeCombinator::union($valueType, new StringType());
                    },
                    $constantArray->getValueTypes()
                )
            );
        }

        return TypeCombinator::union(...$returnType);
    }
}
