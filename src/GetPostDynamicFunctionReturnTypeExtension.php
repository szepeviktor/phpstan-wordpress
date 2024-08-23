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
use WP_Comment;

class GetPostDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    /** @var string */
    private $fullyQualifiedName = WP_Post::class;

    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return in_array(
            $functionReflection->getName(),
            [
                'get_post',
                'get_comment',
            ],
            true
        );
    }

    /**
     * @see https://developer.wordpress.org/reference/functions/get_post/
     */
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): ?Type
    {
        $args = $functionCall->getArgs();

        if ($functionReflection->getName() === 'get_comment') {
            $this->fullyQualifiedName = WP_Comment::class;
        }

        // When called with an instance of WP_Post
        if (
            count($args) > 0 &&
            (new ObjectType($this->fullyQualifiedName))->isSuperTypeOf($scope->getType($args[0]->value))->yes()
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
