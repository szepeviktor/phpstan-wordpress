<?php

/**
 * Set specified type of WP_UnitTestCase_Base::assertWPError().
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

class AssertWpErrorTypeSpecifyingExtension implements \PHPStan\Type\MethodTypeSpecifyingExtension, \PHPStan\Analyser\TypeSpecifierAwareExtension
{
    /** @var \PHPStan\Analyser\TypeSpecifier */
    private $typeSpecifier;

    public function getClass(): string
    {
        return 'WP_UnitTestCase_Base';
    }

    public function isMethodSupported(MethodReflection $methodReflection, MethodCall $node, TypeSpecifierContext $context): bool
    {
        return strtolower($methodReflection->getName()) === 'assertwperror'
            && isset($node->args[0])
            && $context->null();
    }

    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
    public function specifyTypes(MethodReflection $methodReflection, MethodCall $node, Scope $scope, TypeSpecifierContext $context): SpecifiedTypes
    {
        $args = $node->getArgs();

        return $this->typeSpecifier->create($args[0]->value, new ObjectType('WP_Error'), TypeSpecifierContext::createTruthy());
    }

    public function setTypeSpecifier(TypeSpecifier $typeSpecifier): void
    {
        $this->typeSpecifier = $typeSpecifier;
    }
}
