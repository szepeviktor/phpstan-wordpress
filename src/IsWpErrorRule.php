<?php

/**
 * Custom rule to validate usage of `is_wp_error()`.
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Rules\RuleLevelHelper;
use PHPStan\Type\ObjectType;
use PHPStan\Type\VerbosityLevel;

use function sprintf;

/**
 * @implements \PHPStan\Rules\Rule<\PhpParser\Node\Expr\FuncCall>
 */
class IsWpErrorRule implements \PHPStan\Rules\Rule
{
    /** @var \PHPStan\Rules\RuleLevelHelper */
    protected $ruleLevelHelper;

    public function __construct(
        RuleLevelHelper $ruleLevelHelper
    ) {
        $this->ruleLevelHelper = $ruleLevelHelper;
    }

    public function getNodeType(): string
    {
        return FuncCall::class;
    }

    /**
     * @param \PhpParser\Node\Expr\FuncCall $node
     * @param \PHPStan\Analyser\Scope       $scope
     * @return array<int, \PHPStan\Rules\RuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $name = $node->name;

        if (! ($name instanceof Name)) {
            return [];
        }

        if ($name->toString() !== 'is_wp_error') {
            return [];
        }

        $args = $node->getArgs();

        if (! isset($args[0])) {
            return [];
        }

        $argumentType = $scope->getType($args[0]->value);
        $accepted = $this->ruleLevelHelper->accepts(
            $argumentType,
            new ObjectType(\WP_Error::class),
            $scope->isDeclareStrictTypes()
        );

        if (!$accepted) {
            return [
                RuleErrorBuilder::message(
                    sprintf(
                        'is_wp_error(%s) will always evaluate to false.',
                        $argumentType->describe(VerbosityLevel::typeOnly())
                    )
                )->build(),
            ];
        }

        if ((new ObjectType(\WP_Error::class))->isSuperTypeOf($argumentType)->yes()) {
            return [
                RuleErrorBuilder::message(
                    'is_wp_error(WP_Error) will always evaluate to true.'
                )->build(),
            ];
        }

        return [];
    }
}
