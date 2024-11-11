<?php

/**
 * Custom node visitor to fetch the docblock for a function call.
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Comment\Doc;
use PhpParser\Node;

final class HookDocsVisitor extends \PhpParser\NodeVisitorAbstract
{
    protected ?int $latestStartLine;

    protected ?Doc $latestDocComment;

    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
    public function beforeTraverse(array $nodes): ?array
    {
        $this->latestStartLine = null;
        $this->latestDocComment = null;

        return null;
    }

    public function enterNode(Node $node): ?Node
    {
        if ($node->getStartLine() !== $this->latestStartLine) {
            $this->latestDocComment = null;
        }

        $this->latestStartLine = $node->getStartLine();

        $doc = $node->getDocComment();

        if ($doc !== null) {
            $this->latestDocComment = $doc;
        }

        $node->setAttribute('latestDocComment', $this->latestDocComment);

        return null;
    }
}
