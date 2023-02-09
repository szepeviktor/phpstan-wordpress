<?php

/**
 * Set return type of various functions that support an `$echo` or `$display` parameter.
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\MixedType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\VoidType;

class WpTagCloudDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    /** @var \PHPStan\Type\Type */
    private $argType;

    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return $functionReflection->getName() === 'wp_tag_cloud';
    }

    /**
     * @see https://developer.wordpress.org/reference/functions/wp_tag_cloud/
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): ?Type
    {
        $args = $functionCall->getArgs();

        if (count($args) === 0) {
            return new VoidType();
        }

        $this->argType = $scope->getType($args[0]->value);

        $echoType = $this->getEchoType();
        $formatType = $this->getFormatType();

        if (
            $echoType instanceof ConstantBooleanType &&
            $echoType->getValue() === true
        ) {
            return new VoidType();
        }

        if ($formatType instanceof ConstantStringType) {
            $returnType = $formatType->getValue() === 'array'
                ? new ArrayType(new IntegerType(), new StringType())
                : new StringType();
            return TypeCombinator::union($returnType, new VoidType());
        }

        return TypeCombinator::union(
            new ArrayType(new IntegerType(), new StringType()),
            new StringType(),
            new VoidType()
        );
    }

    private function getEchoType(): Type
    {
        if ($this->argType instanceof ConstantArrayType) {
            $type = self::getTypeFromKey($this->argType, 'echo');
            return $type ?? new ConstantBooleanType(true);
        }
        if ($this->argType instanceof ConstantStringType) {
            $type = self::getTypeFromString($this->argType, 'echo');
            return $type ?? new ConstantBooleanType(true);
        }
        return new MixedType();
    }

    private function getFormatType(): Type
    {
        if ($this->argType instanceof ConstantArrayType) {
            $type = self::getTypeFromKey($this->argType, 'format');
            return $type ?? new ConstantStringType('flat');
        }
        if ($this->argType instanceof ConstantStringType) {
            $type = self::getTypeFromString($this->argType, 'format');
            return $type ?? new ConstantStringType('flat');
        }
        return new MixedType();
    }

    private static function getTypeFromKey(ConstantArrayType $type, string $key): ?Type
    {
        foreach ($type->getKeyTypes() as $index => $keyType) {
            if (
                !($keyType instanceof ConstantStringType) ||
                $keyType->getValue() !== $key
            ) {
                continue;
            }
            return $type->getValueTypes()[$index];
        }
        return null;
    }

    private static function getTypeFromString(ConstantStringType $type, string $key): ?Type
    {
        if (strpos($type->getValue(), "{$key}=") === false) {
            return null;
        }

        parse_str($type->getValue(), $parsed);
        return isset($parsed[$key])
            ? new ConstantStringType($parsed[$key])
            : null;
    }
}
