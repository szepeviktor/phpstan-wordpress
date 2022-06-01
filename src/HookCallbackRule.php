<?php

/**
 * Custom rule to validate the callback function for WordPress core actions and filters.
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Rules\RuleLevelHelper;
use PHPStan\Type\Constant\ConstantIntegerType;
use PHPStan\Type\Constant\ConstantStringType;

/**
 * @implements \PHPStan\Rules\Rule<\PhpParser\Node\Expr\FuncCall>
 */
class HookCallbackRule implements \PHPStan\Rules\Rule
{
    private const SUPPORTED_FUNCTIONS = [
        'add_filter',
        'add_action',
    ];

    /** @var \PHPStan\Rules\RuleLevelHelper */
    protected $ruleLevelHelper;

    /** @var \PhpParser\Node\Expr\FuncCall */
    protected $currentNode;

    /** @var \PHPStan\Analyser\Scope */
    protected $currentScope;

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

        if (!($name instanceof \PhpParser\Node\Name)) {
            return [];
        }

        if (!in_array($name->toString(), self::SUPPORTED_FUNCTIONS, true)) {
            return [];
        }

        $args = $node->getArgs();

        // If we don't have enough arguments, bail out and let PHPStan handle the error:
        if (count($args) < 2) {
            return [];
        }

        list(
            $hookNameArg,
            $callbackArg
        ) = $args;

        $hookNameType = $scope->getType($hookNameArg->value);
        $hookNameValue = null;

        if ($hookNameType instanceof ConstantStringType) {
            $hookNameValue = $hookNameType->getValue();
        }

        $callbackType = $scope->getType($callbackArg->value);

        // If the callback is not valid, bail out and let PHPStan handle the error:
        if ($callbackType->isCallable()->no()) {
            return [];
        }

        $acceptedArgs = 1;

        if (isset($args[3])) {
            $acceptedArgs = null;
            $argumentType = $scope->getType($args[3]->value);

            if ($argumentType instanceof ConstantIntegerType) {
                $acceptedArgs = $argumentType->getValue();
            }
        }

        if ($acceptedArgs !== null) {
            $callbackAcceptor = $callbackType->getCallableParametersAcceptors($scope)[0];

            $callbackParameters = $callbackAcceptor->getParameters();
            $expectedArgs = count($callbackParameters);

            if ($expectedArgs !== $acceptedArgs && ($expectedArgs !== 0 && $acceptedArgs !== 1)) {
                $message = (1 === $expectedArgs)
                    ? 'Callback expects %1$d argument, $accepted_args is set to %2$d.'
                    : 'Callback expects %1$d arguments, $accepted_args is set to %2$d.'
                ;

                return [
                    RuleErrorBuilder::message(
                        sprintf(
                            $message,
                            $expectedArgs,
                            $acceptedArgs
                        )
                    )->build()
                ];
            }
        }

        return [];
    }
}
