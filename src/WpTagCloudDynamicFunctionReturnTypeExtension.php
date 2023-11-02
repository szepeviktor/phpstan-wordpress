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
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\VoidType;

class WpTagCloudDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    /** @var \PHPStan\Type\Type */
    private $argType;

    /** @var bool $isEchoTrue */
    private $isEchoTrue;

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

        if (!$this->hasAnyConstantStringsArrays()) {
            return $this->getReturnType('indetermined');
        }

        // We don't check strings, but if it's empty strings only, default values apply.
        if (count($this->argType->getConstantStrings()) !== 0) {
            return !$this->argType->isNonEmptyString()->no()
                ? new VoidType()
                : $this->getReturnType('indetermined');
        }

        // Now constants arrays are left.

        $this->setIsEchoTrue();

        $formatStr = new ConstantStringType('format');
        if ($this->argType->hasOffsetValueType($formatStr)->no()) {
            return $this->isEchoTrue
                ? new VoidType()
                : $this->getReturnType('formatFlat');
        }

        if ($this->argType->hasOffsetValueType($formatStr)->maybe()) {
            return $this->isEchoTrue
                ? $this->getReturnType('formatArray')
                : $this->getReturnType('indetermined');
        }

        $formats = $this->argType->getOffsetValueType($formatStr)->getConstantStrings();
        if (count($formats) === 0) {
            return $this->isEchoTrue
                ? $this->getReturnType('formatArray')
                : $this->getReturnType('indetermined');
        }

        $returnTypes = [];
        foreach ($formats as $format) {
            if ($format->getValue() === 'array') {
                $returnTypes[] = $this->getReturnType('formatArray');
                continue;
            }
            if (!$this->isEchoTrue) {
                $returnTypes[] = $this->getReturnType('formatFlat');
            }
            $returnTypes[] = new VoidType();
        }
        return TypeCombinator::union(...$returnTypes);
    }

    private function setIsEchoTrue(): void
    {
        $echo = new ConstantStringType('echo');
        if ($this->argType->hasOffsetValueType($echo)->no()) {
            $this->isEchoTrue = true;
            return;
        }
        $this->isEchoTrue = $this->argType->getOffsetValueType($echo)->isTrue()->yes();
    }

    private function hasAnyConstantStringsArrays(): bool
    {
        if (count($this->argType->getConstantStrings()) !== 0) {
            return true;
        }
        return count($this->argType->getConstantArrays()) !== 0;
    }

    /**
     * @param 'formatFlat'|'formatArray'|'indetermined' $type
     */
    private function getReturnType(string $type): Type
    {
        switch ($type) {
            case 'formatFlat':
                return TypeCombinator::union(new StringType(), new VoidType());
            case 'formatArray':
                return TypeCombinator::union(
                    new ArrayType(new IntegerType(), new StringType()),
                    new VoidType()
                );
            case 'indetermined':
                return TypeCombinator::union(
                    $this->getReturnType('formatArray'),
                    $this->getReturnType('formatFlat')
                );
            default:
                throw new \PHPStan\ShouldNotHappenException();
        }
    }
}
