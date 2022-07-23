<?php

/**
 * Custom rule to validate the callback function for WordPress core actions and filters.
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ParametersAcceptor;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Rules\RuleLevelHelper;
use PHPStan\Type\Constant\ConstantIntegerType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\Type;
use PHPStan\Type\VerbosityLevel;
use PHPStan\Type\VoidType;

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
        $this->currentNode = $node;
        $this->currentScope = $scope;

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

        $hookNameType = $scope->getType($args[0]->value);
        $hookNameValue = null;

        if ($hookNameType instanceof ConstantStringType) {
            $hookNameValue = $hookNameType->getValue();
        }

        $callbackType = $scope->getType($args[1]->value);

        // If the callback is not valid, bail out and let PHPStan handle the error:
        if ($callbackType->isCallable()->no()) {
            return [];
        }

        $callbackAcceptor = ParametersAcceptorSelector::selectSingle($callbackType->getCallableParametersAcceptors($scope));

        try {
            $this->validateParamCount($callbackAcceptor, $args[3] ?? null);

            if ($name->toString() === 'add_action') {
                $this->validateActionReturnType($callbackAcceptor);
            } else {
                $this->validateUnknownFilterReturnType($callbackAcceptor);
            }
        } catch (\SzepeViktor\PHPStan\WordPress\HookCallbackException $e) {
            return [RuleErrorBuilder::message($e->getMessage())->build()];
        }

        return [];
    }

    protected function validateParamCount(ParametersAcceptor $callbackAcceptor, ?Arg $arg): void
    {
        $acceptedArgs = 1;

        if (isset($arg)) {
            $acceptedArgs = null;
            $argumentType = $this->currentScope->getType($arg->value);

            if ($argumentType instanceof ConstantIntegerType) {
                $acceptedArgs = $argumentType->getValue();
            }
        }

        if ($acceptedArgs === null) {
            return;
        }

        $callbackParameters = $callbackAcceptor->getParameters();
        $expectedArgs = count($callbackParameters);

        if ($expectedArgs === $acceptedArgs) {
            return;
        }

        if ($expectedArgs === 0 && $acceptedArgs === 1) {
            return;
        }

        $message = ($expectedArgs === 1)
            ? 'Callback expects %1$d argument, $accepted_args is set to %2$d.'
            : 'Callback expects %1$d arguments, $accepted_args is set to %2$d.';

        throw new \SzepeViktor\PHPStan\WordPress\HookCallbackException(
            sprintf(
                $message,
                $expectedArgs,
                $acceptedArgs
            )
        );
    }

    protected function validateActionReturnType(ParametersAcceptor $callbackAcceptor): void
    {
        $acceptingType = new VoidType();
        $acceptedType = $callbackAcceptor->getReturnType();
        $accepted = $this->ruleLevelHelper->accepts(
            $acceptingType,
            $acceptedType,
            true
        );

        if (! $accepted) {
            $acceptedVerbosityLevel = VerbosityLevel::getRecommendedLevelByType($acceptedType);

            $message = sprintf(
                'Action callback returns %s but should not return anything.',
                $acceptedType->describe($acceptedVerbosityLevel)
            );

            throw new \SzepeViktor\PHPStan\WordPress\HookCallbackException($message);
        }
    }

    protected function validateUnknownFilterReturnType(ParametersAcceptor $callbackAcceptor): void
    {
        $returnType = $callbackAcceptor->getReturnType();
        $isVoidSuperType = $returnType->isSuperTypeOf(new VoidType());

        if ($isVoidSuperType->yes()) {
            $message = 'Filter callback return statement is missing.';

            throw new \SzepeViktor\PHPStan\WordPress\HookCallbackException($message);
        }
    }

    protected function validateKnownFilterReturnType(ParametersAcceptor $callbackAcceptor, Type $acceptingType): void
    {
        $acceptedType = $callbackAcceptor->getReturnType();
        $accepted = $this->ruleLevelHelper->accepts(
            $acceptingType,
            $acceptedType,
            true
        );
        $acceptingVerbosityLevel = VerbosityLevel::getRecommendedLevelByType($acceptingType);
        $acceptedVerbosityLevel = VerbosityLevel::getRecommendedLevelByType($acceptedType);

        if ($accepted) {
            return;
        }

        $message = sprintf(
            'Callback should return %1$s but returns %2$s.',
            $acceptingType->describe($acceptingVerbosityLevel),
            $acceptedType->describe($acceptedVerbosityLevel)
        );

        if (! (new VoidType())->accepts($acceptedType, true)->no()) {
            $message = 'Filter callback return statement is missing.';
        }

        throw new \SzepeViktor\PHPStan\WordPress\HookCallbackException($message);
    }
}
