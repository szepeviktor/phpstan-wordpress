<?php

/**
 * Set return type of apply_filters() based on its optional preceding docblock.
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\Type;
use PHPStan\Type\MixedType;

class ApplyFiltersDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    /** @var HookDocBlock */
    protected $hookDocBlock;

    public function __construct(HookDocBlock $hookDocBlock)
    {
        $this->hookDocBlock = $hookDocBlock;
    }

    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return in_array(
            $functionReflection->getName(),
            [
                'apply_filters',
                'apply_filters_deprecated',
                'apply_filters_ref_array',
            ],
            true
        );
    }

    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): Type
    {
        $default = new MixedType();
        $resolvedPhpDoc = $this->hookDocBlock->getNullableHookDocBlock($functionCall, $scope);

        if ($resolvedPhpDoc === null) {
            return $default;
        }

        // Fetch the `@param` values from the docblock.
        $params = $resolvedPhpDoc->getParamTags();

        foreach ($params as $param) {
            return $param->getType();
        }

        return $default;
    }
}
