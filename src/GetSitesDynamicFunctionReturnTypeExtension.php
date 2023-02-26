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
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\Constant\ConstantStringType;
use WP_Site;

class GetSitesDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return $functionReflection->getName() === 'get_sites';
    }

    /**
     * @see https://developer.wordpress.org/reference/functions/get_sites/
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): Type
    {
        $args = $functionCall->getArgs();

        // Called without arguments
        if (count($args) === 0) {
            return self::getDefaultType();
        }

        $argumentType = $scope->getType($args[0]->value);

        // Called with a non constant argument
        if (
            count($argumentType->getConstantArrays()) === 0 &&
            count($argumentType->getConstantStrings()) === 0
        ) {
            return self::getIndeterminedType();
        }

        $fields = [];
        $count = [];

        // Called with a constant array argument
        if (count($argumentType->getConstantArrays()) !== 0) {
            foreach ($argumentType->getConstantArrays() as $constantArray) {
                self::getValuesFromArray($constantArray, $fields, $count);
            }
        }

        // Called with a constant string argument
        if (count($argumentType->getConstantStrings()) !== 0) {
            foreach ($argumentType->getConstantStrings() as $constantString) {
                self::getValuesFromString($constantString, $fields, $count);
            }
        }

        return TypeCombinator::union(...self::getReturnTypeFromArgs($fields, $count));
    }

    /**
     * @param list<mixed> $fields
     * @param list<mixed> $count
     * @return list<IntegerType|ArrayType>
     */
    private static function getReturnTypeFromArgs(array $fields, array $count): array
    {
        if (in_array(true, $count, true) && count($count) === 1) {
            return [new IntegerType()];
        }

        $returnType = [];

        if (in_array(true, $count, true)) {
            $returnType[] = new IntegerType();
        }

        if (in_array('ids', $fields, true)) {
            $returnType[] = new ArrayType(new IntegerType(), new IntegerType());
        }

        if (
            (in_array('ids', $fields, true) && count($fields) > 1) ||
            (!in_array('ids', $fields, true) && count($fields) > 0)
        ) {
            $returnType[] = self::getDefaultType();
        }

        return $returnType;
    }

    /**
     * @param list<mixed> $fields
     * @param list<mixed> $count
     */
    private static function getValuesFromArray(ConstantArrayType $constantArray, array &$fields, array &$count): void
    {
        foreach ($constantArray->getKeyTypes() as $index => $key) {
            if (count($key->getConstantStrings()) === 0) {
                continue;
            }
            foreach ($key->getConstantStrings() as $constantKey) {
                if (!in_array($constantKey->getValue(), ['fields', 'count'], true)) {
                    continue;
                }
                $fieldsType = $constantArray->getValueTypes()[$index];
                if (count($fieldsType->getConstantScalarValues()) === 0) {
                    continue;
                }

                foreach ($fieldsType->getConstantScalarTypes() as $constantField) {
                    if ($constantKey->getValue() === 'fields') {
                        $fields[] = $constantField->getValue();
                    }
                    if ($constantKey->getValue() !== 'count') {
                        continue;
                    }

                    $count[] = (bool)$constantField->getValue();
                }
            }
        }

        // If fields and count are not set, add their default value.
        if ($fields === []) {
            $fields = [''];
        }
        if ($count !== []) {
            return;
        }

        $count = [false];
    }

    /**
     * @param list<mixed> $fields
     * @param list<mixed> $count
     */
    private static function getValuesFromString(ConstantStringType $constantString, array &$fields, array &$count): void
    {
        parse_str($constantString->getValue(), $variables);
        $fields[] = $variables['fields'] ?? '';
        $count[] = isset($variables['count']) ? (bool)$variables['count'] : false;
    }

    private static function getIndeterminedType(): Type
    {
        return TypeCombinator::union(
            new ArrayType(new IntegerType(), new ObjectType(WP_Site::class)),
            new ArrayType(new IntegerType(), new IntegerType()),
            new IntegerType()
        );
    }

    private static function getDefaultType(): ArrayType
    {
        return new ArrayType(new IntegerType(), new ObjectType(WP_Site::class));
    }
}
