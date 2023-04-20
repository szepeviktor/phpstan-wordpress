<?php

/**
 * Set return type of various functions that support a `$wp_error` parameter.
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDoc\TypeStringResolver;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\Type;

class WPErrorParameterDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    private const SUPPORTED_FUNCTIONS = [
        '_set_cron_array' => [
            'arg' => 1,
            'false' => 'bool',
            'true' => 'true|WP_Error',
            'maybe' => 'bool|WP_Error',
        ],
    ];

    /** @var \PHPStan\PhpDoc\TypeStringResolver */
    protected $typeStringResolver;

    public function __construct(TypeStringResolver $typeStringResolver)
    {
        $this->typeStringResolver = $typeStringResolver;
    }

    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return array_key_exists($functionReflection->getName(), self::SUPPORTED_FUNCTIONS);
    }

    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): Type
    {
        $name = $functionReflection->getName();
        $functionTypes = self::SUPPORTED_FUNCTIONS[$name] ?? null;
        $args = $functionCall->getArgs();

        if ($functionTypes === null) {
            throw new \PHPStan\ShouldNotHappenException(
                sprintf(
                    'Could not detect return types for function %s()',
                    $name
                )
            );
        }

        $wpErrorArgumentType = new ConstantBooleanType(false);
        $wpErrorArgument = $args[$functionTypes['arg']] ?? null;

        if ($wpErrorArgument !== null) {
            $wpErrorArgumentType = $scope->getType($wpErrorArgument->value);
        }

        // When called with a $wp_error parameter that isn't a constant boolean, return default type
        $type = $functionTypes['maybe'];

        if ($wpErrorArgumentType->isTrue()->yes()) {
            $type = $functionTypes['true'];
        } elseif ($wpErrorArgumentType->isFalse()->yes()) {
            $type = $functionTypes['false'];
        }

        return $this->typeStringResolver->resolve($type);
    }
}
