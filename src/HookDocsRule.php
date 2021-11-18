<?php

/**
 * Custom rule to validate a PHPDoc docblock that precedes a hook.
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Rules\RuleLevelHelper;
use PHPStan\Type\FileTypeMapper;
use PHPStan\Type\VerbosityLevel;

/**
 * @implements \PHPStan\Rules\Rule<\PhpParser\Node\Expr\FuncCall>
 */
class HookDocsRule implements \PHPStan\Rules\Rule
{
    private const SUPPORTED_FUNCTIONS = [
        'apply_filters',
        'apply_filters_deprecated',
        'do_action',
        'do_action_deprecated',
    ];

    /** @var \SzepeViktor\PHPStan\WordPress\HookDocBlock */
    protected $hookDocBlock;

    /** @var \PHPStan\Rules\RuleLevelHelper */
    protected $ruleLevelHelper;

    public function __construct(
        FileTypeMapper $fileTypeMapper,
        RuleLevelHelper $ruleLevelHelper
    ) {
        $this->hookDocBlock = new HookDocBlock($fileTypeMapper);
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
        $name = $node->name->toString();

        if (!in_array($name, self::SUPPORTED_FUNCTIONS, true)) {
            return [];
        }

        $resolvedPhpDoc = $this->hookDocBlock->getNullableHookDocBlock($node, $scope);

        if ($resolvedPhpDoc === null) {
            return [];
        }

        // Fetch the `@param` tags from the docblock.
        $paramTags = $resolvedPhpDoc->getParamTags();

        $numberOfParams = count($node->args) - 1;
        $numberOfParamTags = count($paramTags);

        if ($numberOfParamTags === 0) {
            return [];
        }

        // Incorrect number of `@param` tags.
        if ($numberOfParams !== $numberOfParamTags) {
            $message = sprintf(
                'Expected %1$d @param tags, found %2$d',
                $numberOfParams,
                $numberOfParamTags
            );

            if ($numberOfParams - 1 === $numberOfParamTags) {
                foreach ($node->args as $param) {
                    if ($param->value->name === 'this') {
                        // PHPStan does not detect param tags named `$this`, it skips the tag.
                        // We can indirectly detect this by checking the actual parameter name,
                        // and if one of them is `$this` assume that's the problem.
                        $message = '@param tag must not be named $this. Choose a descriptive alias.';
                        break;
                    }
                }
            }

            // If the number of param tags doesn't match the number of
            // parameters, bail out early with an error. There's no point
            // trying to reconcile param tags in this situation.
            return [
                RuleErrorBuilder::message($message)->build(),
            ];
        }

        $errors = [];
        $i = 1;

        foreach ($paramTags as $paramName => $paramTag) {
            $param = $node->args[$i];
            $paramTagType = $paramTag->getType();
            $paramType = $scope->getType($param->value);
            $accepted = $this->ruleLevelHelper->accepts(
                $paramTagType,
                $paramType,
                $scope->isDeclareStrictTypes()
            );

            if (! $accepted) {
                $paramTagVerbosityLevel = VerbosityLevel::getRecommendedLevelByType($paramTagType);
                $paramVerbosityLevel = VerbosityLevel::getRecommendedLevelByType($paramType);

                $message = sprintf(
                    '@param %1$s $%2$s does not accept actual type of parameter: %3$s',
                    $paramTagType->describe($paramTagVerbosityLevel),
                    $paramName,
                    $paramType->describe($paramVerbosityLevel)
                );

                $errors[] = RuleErrorBuilder::message($message)->build();
            }

            $i += 1;
        }

        return $errors;
    }
}
