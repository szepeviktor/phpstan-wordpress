<?php

/**
 * Set return type of get_comment().
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\Type;
use PHPStan\Type\ArrayType;
use PHPStan\Type\StringType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\NullType;
use PHPStan\Type\TypeCombinator;
use WP_Comment;

class GetCommentDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return $functionReflection->getName() === 'get_comment';
    }

    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): ?Type
    {
        $args = $functionCall->getArgs();
        $returnType = [new NullType()];

        if (
            count($args) > 0 &&
            (new ObjectType(WP_Comment::class))->isSuperTypeOf($scope->getType($args[0]->value))->yes()
        ) {
            $returnType = [];
        }

        if (count($args) < 2) {
            $returnType[] = new ObjectType(WP_Comment::class);
        }

        if (count($args) >= 2) {
            $outputType = $scope->getType($args[1]->value);

            // When called with an $output that isn't a constant string, return default return type
            if (count($outputType->getConstantStrings()) === 0) {
                if ($returnType === []) {
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

            foreach ($outputType->getConstantStrings() as $constantString) {
                switch ($constantString->getValue()) {
                    case 'ARRAY_A':
                        $returnType[] = new ArrayType(new StringType(), new MixedType());
                        break;
                    case 'ARRAY_N':
                        $returnType[] = new ArrayType(new IntegerType(), new MixedType());
                        break;
                    default:
                        $returnType[] = new ObjectType(WP_Comment::class);
                }
            }
        }

        return TypeCombinator::union(...$returnType);
    }
}
