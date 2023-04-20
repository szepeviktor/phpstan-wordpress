<?php

/**
 * Set return type of get_post().
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use WP_Post;

class GetPostDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return $functionReflection->getName() === 'get_post';
    }

    /**
     * @see https://developer.wordpress.org/reference/functions/get_post/
     */
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): ?Type
    {
        $args = $functionCall->getArgs();

        // When called with an instance of WP_Post
        if (
            count($args) > 0 &&
            (new ObjectType(WP_Post::class))->isSuperTypeOf($scope->getType($args[0]->value))->yes()
        ) {
            return TypeCombinator::removeNull(
                ParametersAcceptorSelector::selectFromArgs(
                    $scope,
                    $args,
                    $functionReflection->getVariants()
                )->getReturnType()
            );
        }

        return null;
    }
}
