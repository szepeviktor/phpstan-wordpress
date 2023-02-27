<?php

/**
 * Set specified type of is_wp_error().
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Analyser\SpecifiedTypes;
use PHPStan\Analyser\TypeSpecifier;
use PHPStan\Analyser\TypeSpecifierContext;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\ObjectType;

class IsWpErrorFunctionTypeSpecifyingExtension implements \PHPStan\Type\FunctionTypeSpecifyingExtension, \PHPStan\Analyser\TypeSpecifierAwareExtension
{
    /** @var \PHPStan\Analyser\TypeSpecifier */
    private $typeSpecifier;

    public function isFunctionSupported(FunctionReflection $functionReflection, FuncCall $node, TypeSpecifierContext $context): bool
    {
        return strtolower($functionReflection->getName()) === 'is_wp_error'
            && isset($node->args[0])
            && !$context->null();
    }

    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
    public function specifyTypes(FunctionReflection $functionReflection, FuncCall $node, Scope $scope, TypeSpecifierContext $context): SpecifiedTypes
    {
        if ($context->null()) {
            throw new \PHPStan\ShouldNotHappenException();
        }

        $args = $node->getArgs();

        return $this->typeSpecifier->create($args[0]->value, new ObjectType('WP_Error'), $context);
    }

    public function setTypeSpecifier(TypeSpecifier $typeSpecifier): void
    {
        $this->typeSpecifier = $typeSpecifier;
    }
}
