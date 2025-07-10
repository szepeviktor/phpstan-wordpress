<?php

/**
 * Set specified type of WP_UnitTestCase_Base::assertWPError and
 * WP_UnitTestCase_Base::assertNotWPError.
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Analyser\SpecifiedTypes;
use PHPStan\Analyser\TypeSpecifier;
use PHPStan\Analyser\TypeSpecifierContext;
use PHPStan\Reflection\MethodReflection;

class AssertWpErrorTypeSpecifyingExtension implements \PHPStan\Type\MethodTypeSpecifyingExtension, \PHPStan\Analyser\TypeSpecifierAwareExtension
{
    private const ASSERT = 'assertWPError';
    private const ASSERT_NOT = 'assertNotWPError';

    private TypeSpecifier $typeSpecifier;

    public function getClass(): string
    {
        return 'WP_UnitTestCase_Base';
    }

    public function isMethodSupported(MethodReflection $methodReflection, MethodCall $node, TypeSpecifierContext $context): bool
    {
        return in_array($methodReflection->getName(), [self::ASSERT, self::ASSERT_NOT], true)
            && isset($node->args[0])
            && $context->null();
    }

    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
    public function specifyTypes(MethodReflection $methodReflection, MethodCall $node, Scope $scope, TypeSpecifierContext $context): SpecifiedTypes
    {
        $expression = new Instanceof_($node->getArgs()[0]->value, new Name('WP_Error'));

        if ($methodReflection->getName() === self::ASSERT_NOT) {
            $expression = new BooleanNot($expression);
        }

        return $this->typeSpecifier->specifyTypesInCondition(
            $scope,
            $expression,
            TypeSpecifierContext::createTruthy(),
        );
    }

    public function setTypeSpecifier(TypeSpecifier $typeSpecifier): void
    {
        $this->typeSpecifier = $typeSpecifier;
    }
}
