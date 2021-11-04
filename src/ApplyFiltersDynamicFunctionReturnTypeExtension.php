<?php

/**
 * Set return type of apply_filters().
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDoc\PhpDocStringResolver;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TypeParser;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\Type;
use PHPStan\Type\MixedType;

class ApplyFiltersDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return in_array(
            $functionReflection->getName(),
            [
                'apply_filters',
            ],
            true
        );
    }

    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): Type
    {
        $default = new MixedType();

        /** @var \PhpParser\Node\Expr\Assign|null */
        $parent = $functionCall->getAttribute( 'parent' );

        if (null === $parent) {
            return $default;
        }

        // Fetch the docblock from the parent.
        $comment = $parent->getDocComment();

        if (null === $comment) {
            return $default;
        }

        // Fetch the docblock contents and resolve it to a PhpDocNode.
        $code = $comment->getText();
        $docResolver = new PhpDocStringResolver( new Lexer(), new PhpDocParser( new TypeParser(), new ConstExprParser() ) );
        $doc = $docResolver->resolve($code);

        // Fetch the `@param` values from the docblock.
        $params = $doc->getParamTagValues();

        if (! $params) {
            return $default;
        }

        // Fetch the `@param` types as a TypeNode.
        $type = $params[0]->type;

        // @TODO Fetch and return the Type that corresponds to the TypeNode.

        return $default;
    }
}
