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

    /**
     * Functions that do not accept a query string.
     */
    private const STRICTLY_ARRAY = [
        'get_search_form',
        'wp_login_form',
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
        return array_key_exists($functionReflection->getName(), self::SUPPORTED_FUNCTIONS);
    }

    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): ?Type
    {
        $this->reflection = $functionReflection;
        $this->scope = $scope;
        $this->args = $functionCall->getArgs();
        $this->name = $functionReflection->getName();
        $this->paramPos = self::SUPPORTED_FUNCTIONS[$this->name] ?? null;
        $this->defaultType = $this->getDefaultReturnType();

        if (!isset($this->args[$this->paramPos])) {
            return self::getEchoTrueReturnType($this->name);
        }

        $echoType = $this->getEchoType();

        if ($echoType instanceof ConstantBooleanType) {
            return ($echoType->getValue() === false)
                ? $this->getEchoFalseReturnType()
                : $this->getEchoTrueReturnType();
        }

        if (
            !in_array($this->name, self::STRICTLY_BOOL, true) &&
            $echoType instanceof ConstantIntegerType
        ) {
            return ($echoType->getValue() === 0)
                ? $this->getEchoFalseReturnType()
                : $this->getEchoTrueReturnType();
        }

        return TypeCombinator::union($this->defaultType, new VoidType());
    }

    private function getEchoType(): Type
    {
        $argumentType = $this->scope->getType($this->args[$this->paramPos]->value);

        if ($argumentType instanceof ConstantArrayType) {
            foreach ($argumentType->getKeyTypes() as $index => $key) {
                if (
                    !($key instanceof ConstantStringType) ||
                    $key->getValue() !== 'echo'
                ) {
                    continue;
                }
                return $argumentType->getValueTypes()[$index];
            }
        }

        if (
            !in_array($this->name, self::STRICTLY_ARRAY, true) &&
            $argumentType instanceof ConstantStringType
        ) {
            if (strpos($argumentType->getValue(), 'echo=') === false) {
                return new ConstantBooleanType(true);
            }

            parse_str($argumentType->getValue(), $parsedArgs);
            if (!$parsedArgs['echo']) {
                return new ConstantBooleanType(false);
            }
            if (is_numeric($parsedArgs['echo'])) {
                return new ConstantIntegerType((int)$parsedArgs['echo']);
            }
            if ($parsedArgs['echo'] === 'false') {
                return new ConstantBooleanType(false);
            }
            if ($parsedArgs['echo'] === 'true') {
                return new ConstantBooleanType(true);
            }
        }

        return new NullType();
    }

    private function getEchoFalseReturnType(): Type
    {
        // These function can return void even if echo is not true/truthy.
        $doNotRemove = [
            'the_title_attribute',
            'wp_dropdown_languages',
            'wp_get_archives',
            'wp_list_comments',
        ];

        if (!in_array($this->name, $doNotRemove, true)) {
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
