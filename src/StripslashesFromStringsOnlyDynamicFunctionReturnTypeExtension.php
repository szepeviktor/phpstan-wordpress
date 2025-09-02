<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeTraverser;

class StripslashesFromStringsOnlyDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return $functionReflection->getName() === 'stripslashes_from_strings_only';
    }

    /**
     * @see https://developer.wordpress.org/reference/functions/stripslashes_from_strings_only/
     */
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): ?Type
    {
        if (count($functionCall->getArgs()) === 0) {
            return null;
        }

        $argType = $scope->getType($functionCall->getArgs()[0]->value);

        return TypeTraverser::map(
            $argType,
            static function (Type $type): Type {
                if ($type instanceof ConstantStringType) {
                    return new ConstantStringType(stripslashes($type->getValue()));
                }
                return $type;
            }
        );
    }
}
