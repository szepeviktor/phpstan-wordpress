<?php

/**
 * Set return type of apply_filters() based on its optional preceding docblock.
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDoc\ResolvedPhpDocBlock;
use PHPStan\Type\FileTypeMapper;

class HookDocBlock
{
    protected FileTypeMapper $fileTypeMapper;

    public function __construct(FileTypeMapper $fileTypeMapper)
    {
        $this->fileTypeMapper = $fileTypeMapper;
    }

    public function getNullableHookDocBlock(FuncCall $functionCall, Scope $scope): ?ResolvedPhpDocBlock
    {
        $comment = self::getNullableNodeComment($functionCall);

        if ($comment === null) {
            return null;
        }

        // Fetch the docblock contents.
        $code = $comment->getText();

        // Resolve the docblock in scope.
        $classReflection = $scope->getClassReflection();
        $traitReflection = $scope->getTraitReflection();

        return $this->fileTypeMapper->getResolvedPhpDoc(
            $scope->getFile(),
            ($scope->isInClass() && $classReflection !== null) ? $classReflection->getName() : null,
            ($scope->isInTrait() && $traitReflection !== null) ? $traitReflection->getName() : null,
            $scope->getFunctionName(),
            $code
        );
    }

    private static function getNullableNodeComment(FuncCall $node): ?\PhpParser\Comment\Doc
    {
        /** @var \PhpParser\Comment\Doc|null */
        return $node->getAttribute('latestDocComment');
    }
}
