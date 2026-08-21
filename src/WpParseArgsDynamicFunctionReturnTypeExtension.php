<?php

/**
 * Set return type of wp_parse_args().
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;

final class WpParseArgsDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return $functionReflection->getName() === 'wp_parse_args';
    }

    /**
     * @see https://developer.wordpress.org/reference/functions/wp_parse_args/
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): ?Type
    {
        $args = $functionCall->getArgs();

        if ($args === []) {
            return null;
        }

        // Unpacked arguments are not matched up with the parameters here.
        foreach ($args as $arg) {
            if ($arg->unpack) {
                return null;
            }
        }

        // wp_parse_args() first turns its arguments into an array: an array is taken as it is,
        // an object is converted with get_object_vars(), and anything else is parsed as a query
        // string, which results in a shape we cannot describe.
        $parsed = self::getParsedArgsExpr($args[0]->value, $scope);

        if (! $parsed instanceof Expr) {
            return null;
        }

        $parsedType = $scope->getType($parsed);

        if (! isset($args[1])) {
            return $parsedType;
        }

        $defaultsType = $scope->getType($args[1]->value);

        // Defaults that are not an array are ignored, the parsed arguments are returned as they are.
        if ($defaultsType->isArray()->no()) {
            return $parsedType;
        }

        if (! $defaultsType->isArray()->yes()) {
            return null;
        }

        // The parsed arguments are merged over the defaults, which is what array_merge() does.
        $mergedType = $scope->getType(
            new FuncCall(
                new Name('array_merge'),
                [new Arg($args[1]->value), new Arg($parsed)]
            )
        );

        // Empty defaults are skipped, so the parsed arguments keep their own keys in that case.
        if ($defaultsType->isIterableAtLeastOnce()->yes()) {
            return $mergedType;
        }

        return TypeCombinator::union($mergedType, $parsedType);
    }

    private static function getParsedArgsExpr(Expr $argsExpr, Scope $scope): ?Expr
    {
        $argsType = $scope->getType($argsExpr);

        if ($argsType->isArray()->yes()) {
            return $argsExpr;
        }

        if ($argsType->isObject()->yes()) {
            return new FuncCall(new Name('get_object_vars'), [new Arg($argsExpr)]);
        }

        return null;
    }
}
