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
        $this->currentScope = $scope;

        if (!($name instanceof \PhpParser\Node\Name)) {
            return [];
        }

        if (!in_array($name->toString(), self::SUPPORTED_FUNCTIONS, true)) {
            return [];
        }

        $args = $node->getArgs();

        // If we don't have enough arguments, let PHPStan handle the error:
        if (count($args) < 2) {
            return [];
        }

        $callbackType = $scope->getType($args[1]->value);

        // If the callback is not valid, let PHPStan handle the error:
        if (! $callbackType->isCallable()->yes()) {
            return [];
        }

        $callbackAcceptor = ParametersAcceptorSelector::selectSingle($callbackType->getCallableParametersAcceptors($scope));

        try {
            $this->validateParamCount($callbackAcceptor, $args[3] ?? null);

            if ($name->toString() === 'add_action') {
                $this->validateActionReturnType($callbackAcceptor);
            } else {
                $this->validateFilterReturnType($callbackAcceptor);
            }
        } catch (\SzepeViktor\PHPStan\WordPress\HookCallbackException $e) {
            return [RuleErrorBuilder::message($e->getMessage())->build()];
        }

        return [];
    }

    protected function validateParamCount(ParametersAcceptor $callbackAcceptor, ?Arg $arg): void
    {
        $acceptedArgsParam = 1;

        if (isset($arg)) {
            $acceptedArgsParam = null;
            $argumentType = $this->currentScope->getType($arg->value);

            if ($argumentType instanceof ConstantIntegerType) {
                $acceptedArgsParam = $argumentType->getValue();
            }
        }

        if ($acceptedArgsParam === null) {
            return;
        }

        $allParameters = $callbackAcceptor->getParameters();
        $requiredParameters = array_filter(
            $allParameters,
            static function (\PHPStan\Reflection\ParameterReflection $parameter): bool {
                return ! $parameter->isOptional();
            }
        );
        $expectedArgs = count($allParameters);
        $expectedRequiredArgs = count($requiredParameters);

        if (($acceptedArgsParam >= $expectedRequiredArgs) && ($acceptedArgsParam <= $expectedArgs)) {
            return;
        }

        if ($expectedArgs === 0 && $acceptedArgsParam === 1) {
            return;
        }

        $expectedParametersMessage = $expectedArgs;

        if ($expectedArgs !== $expectedRequiredArgs) {
            $expectedParametersMessage = sprintf(
                '%1$d-%2$d',
                $expectedRequiredArgs,
                $expectedArgs
            );
        }

        $message = ($expectedParametersMessage === 1)
            ? 'Callback expects %1$d parameter, $accepted_args is set to %2$d.'
            : 'Callback expects %1$s parameters, $accepted_args is set to %2$d.';

        throw new \SzepeViktor\PHPStan\WordPress\HookCallbackException(
            sprintf(
                $message,
                $expectedParametersMessage,
                $acceptedArgsParam
            )
        );
    }

    protected function validateActionReturnType(ParametersAcceptor $callbackAcceptor): void
    {
        $acceptedType = $callbackAcceptor->getReturnType();
        $accepted = $this->ruleLevelHelper->accepts(
            new VoidType(),
            $acceptedType,
            true
        );

        if ($accepted) {
            return;
        }

        throw new \SzepeViktor\PHPStan\WordPress\HookCallbackException(
            sprintf(
                'Action callback returns %s but should not return anything.',
                $acceptedType->describe(VerbosityLevel::getRecommendedLevelByType($acceptedType))
            )
        );
    }

    protected function validateFilterReturnType(ParametersAcceptor $callbackAcceptor): void
    {
        $returnType = $callbackAcceptor->getReturnType();
        $isVoidSuperType = $returnType->isSuperTypeOf(new VoidType());

        if (! $isVoidSuperType->yes()) {
            return;
        }

        throw new \SzepeViktor\PHPStan\WordPress\HookCallbackException(
            'Filter callback return statement is missing.'
        );
    }
}
