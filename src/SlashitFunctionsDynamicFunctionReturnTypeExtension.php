<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\Expr\TypeExpr;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\Accessory\AccessoryNonFalsyStringType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\GeneralizePrecision;
use PHPStan\Type\IntersectionType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeTraverser;
use PHPStan\Type\UnionType;

final class SlashitFunctionsDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    use NormalizedArguments;

    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return in_array(
            $functionReflection->getName(),
            [
                'backslashit',
                'trailingslashit',
                'untrailingslashit',
            ],
            true
        );
    }

    /**
     * @see https://developer.wordpress.org/reference/functions/backslashit/
     * @see https://developer.wordpress.org/reference/functions/trailingslashit/
     * @see https://developer.wordpress.org/reference/functions/untrailingslashit/
     */
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): ?Type
    {
        if (count($functionCall->getArgs()) === 0) {
            return null;
        }

        $argType = $scope->isDeclareStrictTypes()
            ? $scope->getType($functionCall->getArgs()[0]->value)
            : $scope->getType($functionCall->getArgs()[0]->value)->toString();

        if (! $argType->isString()->yes()) {
            return null;
        }

        $functionName = $functionReflection->getName();

        if (strpos($functionName, 'trailingslashit') !== false) {
            $type = $scope->getType(new FuncCall(new FullyQualified('rtrim'), [$functionCall->getArgs()[0], new Arg(new String_('/\\'))]));

            if ($functionName === 'untrailingslashit') {
                return $type;
            }

            return $scope->getType(new Concat(new TypeExpr($type), new String_('/')));
        }

        if (! ($functionName === 'backslashit')) {
            return null;
        }

        return TypeTraverser::map(
            $argType,
            static function (Type $type, callable $traverse): Type {
                if ($type instanceof UnionType || $type instanceof IntersectionType) {
                    return $traverse($type);
                }

                if ($type instanceof ConstantStringType) {
                    if ($type->getValue() === '') {
                        return $type;
                    }
                    return $traverse($type->generalize(GeneralizePrecision::moreSpecific()));
                }

                if ($type->isNumericString()->or($type->isNonEmptyString())->yes()) {
                    return new AccessoryNonFalsyStringType();
                }

                return $type;
            }
        );
    }
}
