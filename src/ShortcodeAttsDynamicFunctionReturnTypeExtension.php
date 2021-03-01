<?php

/**
 * Set return type of shortcode_atts().
 */

declare(strict_types=1);

namespace PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\Type;

final class ShortcodeAttsDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return $functionReflection->getName() === 'shortcode_atts';
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

        return $scope->getType($functionCall->args[0]->value);
    }
}
