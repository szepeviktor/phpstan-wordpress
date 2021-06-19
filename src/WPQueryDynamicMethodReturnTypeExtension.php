<?php

/**
 * Set return type of various WP_Query methods.
 */

declare(strict_types=1);

namespace PHPStan\WordPress;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Type;
use PHPStan\Type\ArrayType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\Constant\ConstantStringType;

class WPQueryDynamicMethodReturnTypeExtension implements \PHPStan\Type\DynamicMethodReturnTypeExtension
{

    public function getClass(): string
    {
        return 'WP_Query';
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return in_array($methodReflection->getName(), [
            'get_posts',
            'query',
        ], true);
    }

    /**
     * @see https://developer.wordpress.org/reference/classes/wp_query/#return-fields-parameter
     */
    public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): Type
    {
        // Called without arguments
        if (count($methodCall->args) === 0) {
            return new ArrayType(new IntegerType(), new ObjectType('WP_Post'));
        }

        $argumentType = $scope->getType($methodCall->args[0]->value);

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

        // Without constant argument return default return type
        if (! isset($fields)) {
            return new ArrayType(new IntegerType(), new ObjectType('WP_Post'));
        }

        switch ($fields) {
            case 'ids':
                return new ArrayType(new IntegerType(), new IntegerType());
            case 'id=>parent':
                return new ArrayType(new IntegerType(), new ObjectType('stdClass'));
            case 'all':
            default:
                return new ArrayType(new IntegerType(), new ObjectType('WP_Post'));
        }
    }
}
