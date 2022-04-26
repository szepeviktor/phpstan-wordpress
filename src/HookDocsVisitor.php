<?php

/**
 * Custom node visitor to fetch the docblock for a function call.
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node;

final class HookDocsVisitor extends \PhpParser\NodeVisitorAbstract
{
    /**
     * @var \PhpParser\Comment\Doc|null
     */
    protected $latestDocComment = null;

    public function beforeTraverse(array $nodes): ?array
    {
        $this->latestDocComment = null;

        return null;
    }

    public function enterNode(Node $node): ?Node
    {
        $doc = $node->getDocComment();

        if ($doc !== null) {
            $this->latestDocComment = $doc;
        }

        $node->setAttribute('latestDocComment', $this->latestDocComment);

        return null;
    }
}
