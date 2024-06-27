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
        'checked' => 2,
        'comment_class' => 3,
        'disabled' => 2,
        'edit_term_link' => 4,
        'get_calendar' => 1,
        'menu_page_url' => 1,
        'next_posts' => 1,
        'post_type_archive_title' => 1,
        'previous_posts' => 0,
        'selected' => 2,
        'single_cat_title' => 1,
        'single_month_title' => 1,
        'single_post_title' => 1,
        'single_tag_title' => 1,
        'single_term_title' => 1,
        'the_date' => 3,
        'the_modified_date' => 3,
        'the_title' => 2,
        'wp_loginout' => 1,
        'wp_nonce_field' => 3,
        'wp_original_referer_field' => 0,
        'wp_readonly' => 2,
        'wp_referer_field' => 0,
        'wp_register' => 2,
        'wp_title' => 1,
    ];

    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return array_key_exists($functionReflection->getName(), self::SUPPORTED_FUNCTIONS);
    }

    /**
     * @see https://developer.wordpress.org/reference/functions/checked/
     * @see https://developer.wordpress.org/reference/functions/comment_class/
     * @see https://developer.wordpress.org/reference/functions/disabled/
     * @see https://developer.wordpress.org/reference/functions/edit_term_link/
     * @see https://developer.wordpress.org/reference/functions/get_calendar/
     * @see https://developer.wordpress.org/reference/functions/menu_page_url/
     * @see https://developer.wordpress.org/reference/functions/next_posts/
     * @see https://developer.wordpress.org/reference/functions/post_type_archive_title/
     * @see https://developer.wordpress.org/reference/functions/previous_posts/
     * @see https://developer.wordpress.org/reference/functions/selected/
     * @see https://developer.wordpress.org/reference/functions/single_cat_title/
     * @see https://developer.wordpress.org/reference/functions/single_month_title/
     * @see https://developer.wordpress.org/reference/functions/single_post_title/
     * @see https://developer.wordpress.org/reference/functions/single_tag_title/
     * @see https://developer.wordpress.org/reference/functions/single_term_title/
     * @see https://developer.wordpress.org/reference/functions/the_date/
     * @see https://developer.wordpress.org/reference/functions/the_modified_date/
     * @see https://developer.wordpress.org/reference/functions/the_title/
     * @see https://developer.wordpress.org/reference/functions/wp_loginout/
     * @see https://developer.wordpress.org/reference/functions/wp_nonce_field/
     * @see https://developer.wordpress.org/reference/functions/wp_original_referer_field/
     * @see https://developer.wordpress.org/reference/functions/wp_readonly/
     * @see https://developer.wordpress.org/reference/functions/wp_referer_field/
     * @see https://developer.wordpress.org/reference/functions/wp_register/
     * @see https://developer.wordpress.org/reference/functions/wp_title/
     */
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

        if ($echoArgumentType->isTrue()->yes()) {
            return self::getEchoTrueReturnType($name);
        }
        if ($echoArgumentType->isFalse()->yes()) {
            return self::getEchoFalseReturnType($name);
        }

        return TypeCombinator::union(
            self::getEchoFalseReturnType($name),
            self::getEchoTrueReturnType($name)
        );
    }

    protected static function getEchoTrueReturnType(string $name): Type
    {
        if ($name === 'single_month_title') {
            return TypeCombinator::union(
                new VoidType(),
                new ConstantBooleanType(false)
            );
        }

        return new VoidType();
    }

    protected static function getEchoFalseReturnType(string $name): Type
    {
        if ($name === 'single_month_title') {
            return TypeCombinator::union(
                new StringType(),
                new ConstantBooleanType(false)
            );
        }

        if ($name === 'the_title') {
            return TypeCombinator::union(
                new StringType(),
                new VoidType()
            );
        }

        return new StringType();
    }
}
