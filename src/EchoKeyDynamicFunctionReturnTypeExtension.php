<?php

/**
 * Set return type of various functions that support an optional `$args`
 * of type array with `echo` key that defaults to true|1.
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\Constant\ConstantIntegerType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\NullType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\VoidType;

class EchoKeyDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    /**
     * Function name and position of `$args` parameter.
     */
    private const SUPPORTED_FUNCTIONS = [
        'get_search_form' => 0,
        'the_title_attribute' => 0,
        'wp_dropdown_categories' => 0,
        'wp_dropdown_languages' => 0,
        'wp_dropdown_pages' => 0,
        'wp_dropdown_users' => 0,
        'wp_get_archives' => 0,
        'wp_link_pages' => 0,
        'wp_list_authors' => 0,
        'wp_list_bookmarks' => 0,
        'wp_list_categories' => 0,
        'wp_list_comments' => 0,
        'wp_list_pages' => 0,
        'wp_list_users' => 0,
        'wp_login_form' => 0,
        'wp_page_menu' => 0,
    ];

    /**
     * Functions with strictly boolean `echo` key.
     */
    private const STRICTLY_BOOL = [
        'get_search_form',
        'the_title_attribute',
        'wp_list_authors',
        'wp_list_comments',
        'wp_list_pages',
        'wp_list_users',
        'wp_login_form',
        'wp_page_menu',
    ];

    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return array_key_exists($functionReflection->getName(), self::SUPPORTED_FUNCTIONS);
    }

    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): ?Type
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

        if (!isset($args[$functionParameter])) {
            return self::getEchoTrueReturnType($name);
        }

        $argumentType = $scope->getType($args[$functionParameter]->value);
        $echoType = self::getEchoType($argumentType);
        $defaultType = ParametersAcceptorSelector::selectFromArgs(
            $scope,
            $functionCall->getArgs(),
            $functionReflection->getVariants()
        )->getReturnType();

        if ($echoType instanceof ConstantBooleanType) {
            return ($echoType->getValue() === false)
                ? self::maybeRemoveVoid($name, $defaultType)
                : self::getEchoTrueReturnType($name);
        }

        if (!in_array($name, self::STRICTLY_BOOL, true) && $echoType instanceof ConstantIntegerType) {
            return ($echoType->getValue() === 0)
                ? self::maybeRemoveVoid($name, $defaultType)
                : self::getEchoTrueReturnType($name);
        }

        return TypeCombinator::union($defaultType, new VoidType());
    }

    protected static function getEchoType(Type $argumentType): Type
    {
        $echoType = new ConstantBooleanType(true);

        if ($argumentType instanceof ConstantArrayType) {
            foreach ($argumentType->getKeyTypes() as $index => $key) {
                if (! $key instanceof ConstantStringType || $key->getValue() !== 'echo') {
                    continue;
                }
                $echoType = $argumentType->getValueTypes()[$index];
            }
        }
        return $echoType;
    }

    protected static function maybeRemoveVoid(string $name, Type $type): Type
    {
        // These function can return void even if echo is not true/truthy.
        $doNotRemove = [
            'the_title_attribute',
            'wp_dropdown_languages',
            'wp_get_archives',
            'wp_list_comments',
        ];

        // Fix omitted void type in WP doc block.
        $type = TypeCombinator::union($type, new VoidType());

        if ($name === 'wp_list_users') {
            // null instead of void in WP doc block.
            $type = TypeCombinator::remove($type, new NullType());
        }

        if (!in_array($name, $doNotRemove, true)) {
            $type = TypeCombinator::remove($type, new VoidType());
        }
        return $type;
    }

    protected static function getEchoTrueReturnType( string $name ): Type
    {
        $type = [
            'wp_list_categories' => TypeCombinator::union(
                new VoidType(),
                new ConstantBooleanType(false)
            ),
        ];

        return $type[$name] ?? new VoidType();
    }
}
