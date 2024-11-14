<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\ArgumentsNormalizer;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;

trait NormalizedArguments
{
    /**
     * @return ?array<int, \PhpParser\Node\Arg> $args
     */
    private function getNormalizedFunctionArgs(
        FunctionReflection $functionReflection,
        FuncCall $functionCall,
        Scope $scope
    ): ?array {
        $parametersAcceptor = ParametersAcceptorSelector::selectFromArgs(
            $scope,
            $functionCall->getArgs(),
            $functionReflection->getVariants(),
            $functionReflection->getNamedArgumentsVariants(),
        );

        $normalizedFunctionCall = ArgumentsNormalizer::reorderFuncArguments(
            $parametersAcceptor,
            $functionCall
        );

        if ($normalizedFunctionCall === null) {
            return null;
        }

        return $normalizedFunctionCall->getArgs();
    }
}
