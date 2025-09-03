<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\Constant\ConstantArrayTypeBuilder;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\IntersectionType;
use PHPStan\Type\MixedType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeTraverser;
use PHPStan\Type\UnionType;

class WpSlashDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return in_array(
            $functionReflection->getName(),
            [
                'addslashes_gpc',
                'wp_slash',
            ],
            true
        );
    }

    /**
     * @see https://developer.wordpress.org/reference/functions/addslashes_gpc/
     * @see https://developer.wordpress.org/reference/functions/wp_slash/
     */
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): ?Type
    {
        if (count($functionCall->getArgs()) === 0) {
            return null;
        }

        $argType = $scope->getType($functionCall->getArgs()[0]->value);

        $accepted = new UnionType(
            [
                new StringType(),
                new ArrayType(new MixedType(), new MixedType()),
            ]
        );

        if (! $accepted->isSuperTypeOf($argType)->yes()) {
            return null;
        }

        return $this->addSlashes($argType);
    }

    private function addSlashes(Type $type): Type
    {
        return TypeTraverser::map(
            $type,
            function (Type $type, callable $traverse): Type {
                if ($type instanceof UnionType || $type instanceof IntersectionType) {
                    return $traverse($type);
                }

                if ($type instanceof ConstantStringType) {
                    return new ConstantStringType(addslashes($type->getValue()));
                }

                if (! $type->isArray()->yes()) {
                    return $type;
                }

                if (! ($type instanceof ConstantArrayType)) {
                    return new ArrayType(
                        $type->getIterableKeyType(),
                        $this->addSlashes($type->getIterableValueType())
                    );
                }

                $builder = ConstantArrayTypeBuilder::createEmpty();

                foreach ($type->getKeyTypes() as $index => $keyType) {
                    $builder->setOffsetValueType(
                        $keyType,
                        $this->addSlashes($type->getValueTypes()[$index]),
                        $type->isOptionalKey($index)
                    );
                }

                return $builder->getArray();
            }
        );
    }
}
