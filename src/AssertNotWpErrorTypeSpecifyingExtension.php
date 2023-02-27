<?php

/**
 * Set specified type of WP_UnitTestCase_Base::assertNotWPError().
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Analyser\SpecifiedTypes;
use PHPStan\Analyser\TypeSpecifier;
use PHPStan\Analyser\TypeSpecifierContext;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\ObjectType;
use PHPStan\Type\TypeCombinator;

class AssertNotWpErrorTypeSpecifyingExtension implements \PHPStan\Type\MethodTypeSpecifyingExtension, \PHPStan\Analyser\TypeSpecifierAwareExtension
{
    /** @var \PHPStan\Analyser\TypeSpecifier */
    private $typeSpecifier;

    public function getClass(): string
    {
        return 'WP_UnitTestCase_Base';
    }

    public function isMethodSupported(MethodReflection $methodReflection, MethodCall $node, TypeSpecifierContext $context): bool
    {
        return strtolower($methodReflection->getName()) === 'assertnotwperror'
            && isset($node->args[0])
            && $context->null();
    }

    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
    public function specifyTypes(MethodReflection $methodReflection, MethodCall $node, Scope $scope, TypeSpecifierContext $context): SpecifiedTypes
    {
        $expr = $node->getArgs()[0]->value;
        $typeBefore = $scope->getType($expr);
        $type = TypeCombinator::remove($typeBefore, new ObjectType('WP_Error'));

        return $this->typeSpecifier->create($expr, $type, TypeSpecifierContext::createTruthy());
    }

    public function setTypeSpecifier(TypeSpecifier $typeSpecifier): void
    {
        $this->typeSpecifier = $typeSpecifier;
    }
}
