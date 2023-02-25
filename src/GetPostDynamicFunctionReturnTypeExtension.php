<?php

/**
 * Set return type of get_post().
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\MixedType;
use PHPStan\Type\NullType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use WP_Post;

class GetPostDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return in_array($functionReflection->getName(), ['get_post', 'get_page_by_path'], true);
    }

    /**
     * @see https://developer.wordpress.org/reference/functions/get_post/
     * @see https://developer.wordpress.org/reference/functions/get_page_by_path/
     */
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): Type
    {
        $args = $functionCall->getArgs();
        $returnType = [new NullType()];

        // When called with an instance of WP_Post
        if (
            $functionReflection->getName() === 'get_post' &&
            count($args) > 0 &&
            (new ObjectType(WP_Post::class))->isSuperTypeOf($scope->getType($args[0]->value))->yes()
        ) {
            $returnType = [];
        }

        if (count($args) < 2) {
            $returnType[] = new ObjectType(WP_Post::class);
        }

        if (count($args) >= 2) {
            $outputType = $scope->getType($args[1]->value);

            // When called with an $output that isn't a constant string
            if (count($outputType->getConstantStrings()) === 0) {
                $returnType[] = new ArrayType(new StringType(), new MixedType());
                $returnType[] = new ArrayType(new IntegerType(), new MixedType());
                $returnType[] = new ObjectType(WP_Post::class);
                return TypeCombinator::union(...$returnType);
            }

            foreach ($outputType->getConstantStrings() as $constantString) {
                switch ($constantString->getValue()) {
                    case 'ARRAY_A':
                        $returnType[] = new ArrayType(new StringType(), new MixedType());
                        break;
                    case 'ARRAY_N':
                        $returnType[] = new ArrayType(new IntegerType(), new MixedType());
                        break;
                    default:
                        $returnType[] = new ObjectType(WP_Post::class);
                }
            }
        }

        return TypeCombinator::union(...$returnType);
    }
}
