<?php

/**
 * Custom rule to validate usage of `is_wp_error()`.
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
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

    public function processNode(Node $node, Scope $scope): array
    {
        if (! ($node->name instanceof Name)) {
            return [];
        }

        if ($node->name->toString() !== 'is_wp_error') {
            return [];
        }

        $args = $node->getArgs();

        if (count($args) === 0) {
            return [];
        }

        $argumentType = $scope->getType($args[0]->value);

        $accepted = $this->ruleLevelHelper->accepts(
            $argumentType,
            new ObjectType(\WP_Error::class),
            $scope->isDeclareStrictTypes()
        );

        if (! $accepted) {
            return [
                RuleErrorBuilder::message(
                    sprintf(
                        'is_wp_error(%s) will always evaluate to false.',
                        $argumentType->describe(VerbosityLevel::typeOnly())
                    )
                )->identifier('function.impossibleType')->build(),
            ];
        }

        if ((new ObjectType(\WP_Error::class))->isSuperTypeOf($argumentType)->yes()) {
            return [
                RuleErrorBuilder::message(
                    'is_wp_error(WP_Error) will always evaluate to true.'
                )->identifier('function.alreadyNarrowedType')->build(),
            ];
        }

        return [];
    }
}
