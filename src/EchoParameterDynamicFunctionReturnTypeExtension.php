<?php

/**
 * Set return type of various functions that support an `$echo` or `$display` parameter.
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\VoidType;

class EchoParameterDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    /**
     * Function name and position of `$echo` parameter.
     */
    private const SUPPORTED_FUNCTIONS = [
        'comment_class' => 3,
        'edit_term_link' => 4,
        'get_calendar' => 1,
        'next_posts' => 1,
        'post_type_archive_title' => 1,
        'previous_posts' => 0,
        'single_cat_title' => 1,
        'single_post_title' => 1,
        'single_tag_title' => 1,
        'single_term_title' => 1,
        'the_date' => 3,
        'the_modified_date' => 3,
        'the_title' => 2,
        'wp_loginout' => 1,
        'wp_register' => 2,
        'wp_title' => 1,
    ];

    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return array_key_exists($functionReflection->getName(), self::SUPPORTED_FUNCTIONS);
    }

    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): Type
    {
        $name = $functionReflection->getName();
        $functionParameter = self::SUPPORTED_FUNCTIONS[$name] ?? null;
        $args = $functionCall->getArgs();

        if ($functionParameter === null) {
            throw new \PHPStan\ShouldNotHappenException(
                sprintf(
                    'Could not detect return types for function %s()',
                    $name
                )
            );
        }

        $echoArgumentType = new ConstantBooleanType(true);

        if (isset($args[$functionParameter])) {
            $echoArgumentType = $scope->getType($args[$functionParameter]->value);
        }

        if ($echoArgumentType instanceof ConstantBooleanType) {
            return ($echoArgumentType->getValue() === false)
                ? new StringType()
                : new VoidType();
        }

        return TypeCombinator::union(
            new StringType(),
            new VoidType()
        );
    }
}
