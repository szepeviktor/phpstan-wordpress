<?php

/**
 * Set return type of get_sites().
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\Type;
use PHPStan\Type\ArrayType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\Constant\ConstantStringType;

class GetSitesDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return $functionReflection->getName() === 'get_sites';
    }

    /**
     * @see https://developer.wordpress.org/reference/classes/wp_query/#return-fields-parameter
     */
    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): Type
    {
        $args = $functionCall->getArgs();

        // Called without arguments
        if (count($args) === 0) {
            return new ArrayType(new IntegerType(), new ObjectType('WP_Site'));
        }

        $argumentType = $scope->getType($args[0]->value);

        // Called with an array argument
        if ($argumentType instanceof ConstantArrayType) {
            foreach ($argumentType->getKeyTypes() as $index => $key) {
                if (! $key instanceof ConstantStringType || $key->getValue() !== 'fields') {
                    continue;
                }

                $fieldsType = $argumentType->getValueTypes()[$index];
                if ($fieldsType instanceof ConstantStringType) {
                    $fields = $fieldsType->getValue();
                }
                break;
            }
        }
        // Called with a string argument
        if ($argumentType instanceof ConstantStringType) {
            parse_str($argumentType->getValue(), $variables);
            $fields = $variables['fields'] ?? 'all';
        }

        switch ($fields ?? null) {
            case 'count':
                return new IntegerType();
            case 'ids':
                return new ArrayType(new IntegerType(), new IntegerType());
            default:
                return new ArrayType(new IntegerType(), new ObjectType('WP_Site'));
        }
    }
}
