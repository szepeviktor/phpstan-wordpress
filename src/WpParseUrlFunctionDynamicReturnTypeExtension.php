<?php

/**
 * Set return type of wp_parse_url().
 *
 * Based on ParseUrlFunctionDynamicReturnTypeExtension in PHPStan itself.
 *
 * phpcs:disable WordPress.WP.AlternativeFunctions.parse_url_parse_url
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\Constant\ConstantArrayTypeBuilder;
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\Constant\ConstantIntegerType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\NullType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;

use function count;
use function parse_url;

use const PHP_URL_FRAGMENT;
use const PHP_URL_HOST;
use const PHP_URL_PASS;
use const PHP_URL_PATH;
use const PHP_URL_PORT;
use const PHP_URL_QUERY;
use const PHP_URL_SCHEME;
use const PHP_URL_USER;

final class WpParseUrlFunctionDynamicReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    /** @var array<int, \PHPStan\Type\Type>|null */
    private $componentTypesPairedConstants = null;

    /** @var array<string, \PHPStan\Type\Type>|null */
    private $componentTypesPairedStrings = null;

    /** @var \PHPStan\Type\Type|null */
    private $allComponentsTogetherType = null;

    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return $functionReflection->getName() === 'wp_parse_url';
    }

    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): ?Type
    {
        if (count($functionCall->getArgs()) < 1) {
            return null;
        }

        $this->cacheReturnTypes();

        $componentType = new ConstantIntegerType(-1);
        if (count($functionCall->getArgs()) > 1) {
            $componentType = $scope->getType($functionCall->getArgs()[1]->value);

            if (!$componentType instanceof ConstantIntegerType) {
                return $this->createAllComponentsReturnType();
            }
        }

        $urlType = $scope->getType($functionCall->getArgs()[0]->value);
        if (count($urlType->getConstantStrings()) !== 0) {
            $returnTypes = [];
            foreach ($urlType->getConstantStrings() as $constantString) {
                try {
                    // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
                    $result = @parse_url($constantString->getValue(), $componentType->getValue());
                    $returnTypes[] = $scope->getTypeFromValue($result);
                } catch (\ValueError $e) {
                    $returnTypes[] = new ConstantBooleanType(false);
                }
            }
            return TypeCombinator::union(...$returnTypes);
        }

        if ($componentType->getValue() === -1) {
            return $this->createAllComponentsReturnType();
        }

        return $this->componentTypesPairedConstants[$componentType->getValue()] ?? new ConstantBooleanType(false);
    }

    private function createAllComponentsReturnType(): Type
    {
        if ($this->allComponentsTogetherType === null) {
            $returnTypes = [
                new ConstantBooleanType(false),
            ];

            $builder = ConstantArrayTypeBuilder::createEmpty();

            if ($this->componentTypesPairedStrings === null) {
                throw new \PHPStan\ShouldNotHappenException();
            }

            foreach ($this->componentTypesPairedStrings as $componentName => $componentValueType) {
                $builder->setOffsetValueType(new ConstantStringType($componentName), $componentValueType, true);
            }

            $returnTypes[] = $builder->getArray();

            $this->allComponentsTogetherType = TypeCombinator::union(...$returnTypes);
        }

        return $this->allComponentsTogetherType;
    }

    private function cacheReturnTypes(): void
    {
        if ($this->componentTypesPairedConstants !== null) {
            return;
        }

        $string = new StringType();
        $integer = new IntegerType();
        $false = new ConstantBooleanType(false);
        $null = new NullType();

        $stringOrFalseOrNull = TypeCombinator::union($string, $false, $null);
        $integerOrFalseOrNull = TypeCombinator::union($integer, $false, $null);

        $this->componentTypesPairedConstants = [
            PHP_URL_SCHEME => $stringOrFalseOrNull,
            PHP_URL_HOST => $stringOrFalseOrNull,
            PHP_URL_PORT => $integerOrFalseOrNull,
            PHP_URL_USER => $stringOrFalseOrNull,
            PHP_URL_PASS => $stringOrFalseOrNull,
            PHP_URL_PATH => $stringOrFalseOrNull,
            PHP_URL_QUERY => $stringOrFalseOrNull,
            PHP_URL_FRAGMENT => $stringOrFalseOrNull,
        ];

        $this->componentTypesPairedStrings = [
            'scheme' => $string,
            'host' => $string,
            'port' => $integer,
            'user' => $string,
            'pass' => $string,
            'path' => $string,
            'query' => $string,
            'fragment' => $string,
        ];
    }
}
