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
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\Type;

class WPErrorDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    /**
     * @var array<string, array{0:int,1:string,2:string}>
     */
    private const SUPPORTED_FUNCTIONS = [
        'wp_insert_link' => [1, 'int', 'int|\WP_Error'],
        'wp_insert_category' => [1, 'int', 'int|\WP_Error'],
        'wp_set_comment_status' => [2, 'bool', 'true|\WP_Error'],
        'wp_update_comment' => [1, '0|1|false', '0|1|\WP_Error'],
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
        $func = self::SUPPORTED_FUNCTIONS[$name] ?? null;

        if ($func === null) {
            throw new \PHPStan\ShouldNotHappenException(
                sprintf(
                    'Could not detect return types for function %s',
                    $name
                )
            );
        }

        $argumentType = new ConstantBooleanType(false);
        $arg = $functionCall->args[$func[0]] ?? null;

        if ($arg !== null) {
            $argumentType = $scope->getType($arg->value);
        }

        // When called with a $wp_error parameter that isn't a constant boolean, return default type
        if (!( $argumentType instanceof ConstantBooleanType)) {
            return ParametersAcceptorSelector::selectFromArgs(
                $scope,
                $functionCall->args,
                $functionReflection->getVariants()
            )->getReturnType();
        }

        $type = $func[1];

        if ($argumentType->getValue()) {
            $type = $func[2];
        }

        return $this->typeStringResolver->resolve($type);
    }
}
