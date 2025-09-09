<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\Accessory\AccessoryNonEmptyStringType;
use PHPStan\Type\Accessory\AccessoryType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\GeneralizePrecision;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\TypeUtils;
use PHPStan\Type\UnionType;

final class NormalizeWhitespaceDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return $functionReflection->getName() === 'normalize_whitespace';
    }

    /**
     * @see https://developer.wordpress.org/reference/functions/normalize_whitespace/
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): ?Type
    {
        if (count($functionCall->getArgs()) < 1) {
            return null;
        }

        $argType = $scope->getType($functionCall->getArgs()[0]->value);
        if (! $scope->isDeclareStrictTypes()) {
            $argType = $argType->toString();
        }

        if (! $argType->isString()->yes()) {
            return null;
        }

        $argTypes = $argType instanceof UnionType ? $argType->getTypes() : [$argType];

        $types = [];
        foreach ($argTypes as $type) {
            if ($type->isConstantValue()->yes()) {
                $types[] = $this->getTypeFromConstantString($type->getConstantStrings()[0]);
                continue;
            }

            if ($type->isNonFalsyString()->yes()) {
                $types[] = $this->getTypeFromNonFalsyString($type);
                continue;
            }

            if ($type->isNonEmptyString()->yes()) {
                $types[] = $this->getTypeFromNonEmptyString($type);
                continue;
            }

            $types[] = $type;
        }

        return TypeCombinator::union(...$types);
    }

    private function getTypeFromConstantString(ConstantStringType $type): Type
    {
        $typeValue = $type->getValue();
        $type = new ConstantStringType(trim($typeValue));

        if ($type->isNonEmptyString()->no() || $type->isNonFalsyString()->no()) {
            return $type;
        }

        return $type->generalize(GeneralizePrecision::moreSpecific());
    }

    private function getTypeFromNonFalsyString(Type $type): Type
    {
        $types = array_merge(
            [new StringType(), new AccessoryNonEmptyStringType()],
            array_filter(
                TypeUtils::getAccessoryTypes($type),
                static function (AccessoryType $accessoryType): bool {
                    return ! $accessoryType->isNonFalsyString()->yes();
                }
            )
        );

        return TypeCombinator::intersect(...$types);
    }

    private function getTypeFromNonEmptyString(Type $type): Type
    {
        $types = array_merge(
            [new StringType()],
            array_filter(
                TypeUtils::getAccessoryTypes($type),
                static function (AccessoryType $accessoryType): bool {
                    return ! $accessoryType->isNonEmptyString()->yes();
                }
            )
        );

        return TypeCombinator::intersect(...$types);
    }
}
