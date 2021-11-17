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
use PHPStan\Type\FileTypeMapper;

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

    public function __construct(FileTypeMapper $fileTypeMapper)
    {
        $this->hookDocBlock = new HookDocBlock($fileTypeMapper);
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

        $numberOfParams = count($node->args);
        $numberOfParamTags = count($paramTags);

        // Too few `@param` tags.
        if ($numberOfParams > $numberOfParamTags) {
            $message = sprintf(
                'Expected %1$d @param tags, found %2$d',
                $numberOfParams,
                $numberOfParamTags
            );
            return [
                RuleErrorBuilder::message($message)->build(),
            ];
        }

        return [];
    }
}
