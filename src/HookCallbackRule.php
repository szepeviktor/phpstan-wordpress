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
use PHPStan\Type\MixedType;
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

        $parametersAcceptors = $callbackType->getCallableParametersAcceptors($scope);
        $callbackAcceptor = ParametersAcceptorSelector::selectFromArgs($scope, $args, $parametersAcceptors);

        try {
            if ($name->toString() === 'add_action') {
                $this->validateActionReturnType($callbackAcceptor);
            } else {
                $this->validateFilterReturnType($callbackAcceptor);
            }

            $this->validateParamCount($callbackAcceptor, $args[3] ?? null);
        } catch (\SzepeViktor\PHPStan\WordPress\HookCallbackException $e) {
            return [RuleErrorBuilder::message($e->getMessage())->build()];
        }

        return [];
    }

    protected function getAcceptedArgs(?Arg $acceptedArgsParam): ?int
    {
        $acceptedArgs = 1;

        if (isset($acceptedArgsParam)) {
            $acceptedArgs = null;
            $argumentType = $this->currentScope->getType($acceptedArgsParam->value);

            if ($argumentType instanceof ConstantIntegerType) {
                $acceptedArgs = $argumentType->getValue();
            }
        }

        return $acceptedArgs;
    }

    protected function validateParamCount(ParametersAcceptor $callbackAcceptor, ?Arg $acceptedArgsParam): void
    {
        $acceptedArgs = $this->getAcceptedArgs($acceptedArgsParam);

        if ($acceptedArgs === null) {
            return;
        }

        $allParameters = $callbackAcceptor->getParameters();
        $requiredParameters = array_filter(
            $allParameters,
            static function (\PHPStan\Reflection\ParameterReflection $parameter): bool {
                return ! $parameter->isOptional();
            }
        );
        $maxArgs = count($allParameters);
        $minArgs = count($requiredParameters);

        if (($acceptedArgs >= $minArgs) && ($acceptedArgs <= $maxArgs)) {
            return;
        }

        if (($acceptedArgs >= $minArgs) && $callbackAcceptor->isVariadic()) {
            return;
        }

        if ($minArgs === 0 && $acceptedArgs === 1) {
            return;
        }

        throw new \SzepeViktor\PHPStan\WordPress\HookCallbackException(
            self::buildParameterCountMessage(
                $minArgs,
                $maxArgs,
                $acceptedArgs,
                $callbackAcceptor
            )
        );
    }

    protected static function buildParameterCountMessage(int $minArgs, int $maxArgs, int $acceptedArgs, ParametersAcceptor $callbackAcceptor): string
    {
        $expectedParametersMessage = $minArgs;

        if ($maxArgs !== $minArgs) {
            $expectedParametersMessage = sprintf(
                '%1$d-%2$d',
                $minArgs,
                $maxArgs
            );
        }

        $message = ($expectedParametersMessage === 1)
            ? 'Callback expects %1$d parameter, $accepted_args is set to %2$d.'
            : 'Callback expects %1$s parameters, $accepted_args is set to %2$d.';

        if ($callbackAcceptor->isVariadic()) {
            $message = ($minArgs === 1)
                ? 'Callback expects at least %1$d parameter, $accepted_args is set to %2$d.'
                : 'Callback expects at least %1$s parameters, $accepted_args is set to %2$d.';
        }

        return sprintf(
            $message,
            $expectedParametersMessage,
            $acceptedArgs
        );
    }

    protected function validateActionReturnType(ParametersAcceptor $callbackAcceptor): void
    {
        $acceptedType = $callbackAcceptor->getReturnType();
        $exception = new \SzepeViktor\PHPStan\WordPress\HookCallbackException(
            sprintf(
                'Action callback returns %s but should not return anything.',
                $acceptedType->describe(VerbosityLevel::getRecommendedLevelByType($acceptedType))
            )
        );

        if ($acceptedType instanceof MixedType && $acceptedType->isExplicitMixed()) {
            throw $exception;
        }

        $accepted = $this->ruleLevelHelper->accepts(
            new VoidType(),
            $acceptedType,
            true
        );

        if (! $accepted) {
            throw $exception;
        }
    }

    protected function validateFilterReturnType(ParametersAcceptor $callbackAcceptor): void
    {
        $returnType = $callbackAcceptor->getReturnType();

        if ($returnType instanceof MixedType) {
            return;
        }

        $accepted = $this->ruleLevelHelper->accepts(
            new VoidType(),
            $returnType,
            true
        );

        if (! $accepted) {
            return;
        }

        throw new \SzepeViktor\PHPStan\WordPress\HookCallbackException(
            'Filter callback return statement is missing.'
        );
    }
}
