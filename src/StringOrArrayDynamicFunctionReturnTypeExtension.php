<?php

/**
 * Set return type of esc_sql(), wp_slash() and wp_unslash().
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\StringType;
use PHPStan\Type\ArrayType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\Type;

class StringOrArrayDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return in_array($functionReflection->getName(), ['esc_sql', 'wp_slash', 'wp_unslash'], true);
    }

    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): Type
    {
        $args = $functionCall->getArgs();
        $argsCount = count($args);
        if ($argsCount === 0) {
            return ParametersAcceptorSelector::selectFromArgs(
                $scope,
                $args,
                $functionReflection->getVariants()
            )->getReturnType();
        }
        $dataArg = $args[0]->value;
        $dataArgType = $scope->getType($dataArg);
        if ($dataArgType->isArray()->yes()) {
            $keyType = $dataArgType->getIterableKeyType();
            if ($keyType instanceof StringType) {
                return new ArrayType(new StringType(), new StringType());
            }
            return new ArrayType(new IntegerType(), new StringType());
        }
        return new StringType();
    }
}
