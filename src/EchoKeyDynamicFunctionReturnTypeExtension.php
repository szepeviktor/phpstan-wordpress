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
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\MixedType;
use PHPStan\Type\NullType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\VoidType;

class EchoKeyDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    /**
     * Name of supported function and position of `$args` parameter.
     */
    private const FUNCTIONS = [
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
     * Functions that do not accept a query string.
     */
    private const STRICTLY_ARRAY = [
        'get_search_form',
        'wp_login_form',
    ];

    /**
     * Functions that can return void even if `$echo` is falsey.
     */
    private const ALWAYS_VOID = [
        'the_title_attribute',
        'wp_dropdown_languages',
        'wp_get_archives',
        'wp_list_comments',
    ];

    /** @var \PHPStan\Reflection\FunctionReflection */
    private $reflection;

    /** @var \PHPStan\Analyser\Scope */
    private $scope;

    /** @var array<\PhpParser\Node\Arg> */
    private $args;

    /** @var string */
    private $name;

    /** @var ?int */
    private $paramPos;

    /** @var \PHPStan\Type\Type */
    private $defaultType;

    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return array_key_exists($functionReflection->getName(), self::FUNCTIONS);
    }

    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): ?Type
    {
        $this->reflection = $functionReflection;
        $this->scope = $scope;
        $this->args = $functionCall->getArgs();
        $this->name = $functionReflection->getName();
        $this->paramPos = self::FUNCTIONS[$this->name] ?? null;
        $this->defaultType = $this->getDefaultReturnType();

        if (!isset($this->args[$this->paramPos])) {
            return $this->getEchoTrueReturnType();
        }

        $echoType = $this->getEchoType();
        if ($echoType->isTrue()->yes() || $echoType->isFalse()->yes()) {
            return $echoType->isTrue()->yes()
                ? $this->getEchoTrueReturnType()
                : $this->getEchoFalseReturnType();
        }

        return $this->defaultType;
    }

    private function getEchoType(): Type
    {
        $argType = $this->scope->getType($this->args[$this->paramPos]->value);

        if ($argType->isArray()->yes()) {
            return $argType->getOffsetValueType(new ConstantStringType('echo'))->toBoolean();
        }

        if (
            !in_array($this->name, self::STRICTLY_ARRAY, true) &&
            count($argType->getConstantStrings()) !== 0
        ) {
            return TypeCombinator::union(
                ...array_map(
                    static function (ConstantStringType $constantStringType): ConstantBooleanType {
                        parse_str($constantStringType->getValue(), $parsed);
                        return !isset($parsed['echo'])
                            ? new ConstantBooleanType(true)
                            : new ConstantBooleanType((bool)$parsed['echo']);
                    },
                    $argType->getConstantStrings()
                )
            );
        }

        return new MixedType();
    }

    private function getEchoFalseReturnType(): Type
    {
        if (!in_array($this->name, self::ALWAYS_VOID, true)) {
            return TypeCombinator::remove($this->defaultType, new VoidType());
        }

        return $this->defaultType;
    }

    private function getEchoTrueReturnType(): Type
    {
        $type = [
            'wp_list_categories' => TypeCombinator::union(
                new VoidType(),
                new ConstantBooleanType(false)
            ),
        ];

        return $type[$this->name] ?? new VoidType();
    }

    private function getDefaultReturnType(): Type
    {
        $defaultType = ParametersAcceptorSelector::selectFromArgs(
            $this->scope,
            $this->args,
            $this->reflection->getVariants()
        )->getReturnType();

        // Fix omitted void type in WP doc block.
        $defaultType = TypeCombinator::union($defaultType, new VoidType());

        if ($this->name === 'wp_list_users') {
            // null instead of void in WP doc block.
            $defaultType = TypeCombinator::remove($defaultType, new NullType());
        }

        return $defaultType;
    }
}
