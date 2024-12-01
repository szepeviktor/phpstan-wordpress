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
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\Constant\ConstantIntegerType;
use PHPStan\Type\MixedType;
use PHPStan\Type\NeverType;
use PHPStan\Type\Type;
use PHPStan\Type\VerbosityLevel;

/**
 * @implements \PHPStan\Rules\Rule<\PhpParser\Node\Expr\FuncCall>
 */
class HookCallbackRule implements \PHPStan\Rules\Rule
{
    use NormalizedArguments;

    private const SUPPORTED_FUNCTIONS = [
        'add_filter',
        'add_action',
    ];

    private const CALLBACK_INDEX = 1;

    private const ACCEPTED_ARGS_INDEX = 3;

    private const ACCEPTED_ARGS_DEFAULT = 1;

    /** @var list<\PHPStan\Rules\IdentifierRuleError> */
    private array $errors;

    protected Scope $currentScope;

    private ReflectionProvider $reflectionProvider;

    public function __construct(ReflectionProvider $reflectionProvider)
    {
        $this->reflectionProvider = $reflectionProvider;
    }

    public function getNodeType(): string
    {
        return FuncCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $this->currentScope = $scope;
        $this->errors = [];

        if (! ($node->name instanceof Node\Name)) {
            return [];
        }

        if (! $this->reflectionProvider->hasFunction($node->name, $scope)) {
            return [];
        }

        $functionReflection = $this->reflectionProvider->getFunction($node->name, $scope);

        if (! in_array($functionReflection->getName(), self::SUPPORTED_FUNCTIONS, true)) {
            return [];
        }

        $args = $this->getNormalizedFunctionArgs($functionReflection, $node, $scope);

        // If we don't have enough arguments, let PHPStan handle the error:
        if ($args === null || count($args) < self::CALLBACK_INDEX + 1) {
            return [];
        }

        $callbackType = $scope->getType($args[self::CALLBACK_INDEX]->value);

        // If the callback is not valid, let PHPStan handle the error:
        if (! $callbackType->isCallable()->yes()) {
            return [];
        }

        $callbackAcceptor = ParametersAcceptorSelector::selectFromArgs(
            $scope,
            $args,
            $callbackType->getCallableParametersAcceptors($scope)
        );

        if ($node->name->toString() === 'add_action') {
            $this->validateActionReturnType($callbackAcceptor->getReturnType());
        } else {
            $this->validateFilterReturnType($callbackAcceptor->getReturnType());
        }

        if ($this->errors !== []) {
            return $this->errors;
        }

        $this->validateParamCount($callbackAcceptor, $args[self::ACCEPTED_ARGS_INDEX] ?? null);

        return $this->errors;
    }

    protected function getAcceptedArgs(?Arg $acceptedArgsParam): ?int
    {
        if ($acceptedArgsParam === null) {
            return self::ACCEPTED_ARGS_DEFAULT;
        }

        $argumentType = $this->currentScope->getType($acceptedArgsParam->value);

        if ($argumentType instanceof ConstantIntegerType) {
            return $argumentType->getValue();
        }

        return null;
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

        $this->errors[] = RuleErrorBuilder::message(
            self::buildParameterCountMessage(
                $minArgs,
                $maxArgs,
                $acceptedArgs,
                $callbackAcceptor
            )
        )->identifier('arguments.count')->build();
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

    protected function validateActionReturnType(Type $returnType): void
    {
        // Will be handled by PHPStan.
        if ($returnType instanceof MixedType && ! $returnType->isExplicitMixed()) {
            return;
        }

        if ($returnType->isVoid()->yes() || $this->isExplicitNever($returnType)) {
            return;
        }

        $this->errors[] = RuleErrorBuilder::message(
            sprintf(
                'Action callback returns %s but should not return anything.',
                $returnType->describe(VerbosityLevel::getRecommendedLevelByType($returnType))
            )
        )->identifier('return.void')->build();
    }

    protected function validateFilterReturnType(Type $returnType): void
    {
        if ($returnType instanceof MixedType) {
            return;
        }

        if (($returnType->isVoid()->no()) && ! $this->isExplicitNever($returnType)) {
            return;
        }

        $this->errors[] = RuleErrorBuilder::message(
            'Filter callback return statement is missing.'
        )->identifier('return.missing')->build();
    }

    protected function isExplicitNever(Type $type): bool
    {
        return $type instanceof NeverType && $type->isExplicit();
    }
}
