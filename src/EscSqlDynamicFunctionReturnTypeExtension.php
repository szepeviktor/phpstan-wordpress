<?php

/**
 * Set return type of esc_sql().
 */

declare(strict_types=1);

namespace PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\StringType;
use PHPStan\Type\ArrayType;
use PHPStan\Type\Type;

class EscSqlDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return $functionReflection->getName() === 'esc_sql';
    }

    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): Type
    {
        $argsCount = count($functionCall->args);
        if ($argsCount === 0) {
            return ParametersAcceptorSelector::selectFromArgs(
                $scope,
                $functionCall->args,
                $functionReflection->getVariants()
            )->getReturnType();
        }

        $dataArg = $functionCall->args[0]->value;
        $dataArgType = $scope->getType($dataArg);
        if ($dataArgType instanceof ArrayType) {
            $keyType = $dataArgType->getIterableKeyType();
            $itemType = $dataArgType->getIterableValueType();
            return new ArrayType($keyType, $itemType);
        }
        return new StringType();
    }
}
