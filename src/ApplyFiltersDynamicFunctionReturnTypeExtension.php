<?php

/**
 * Set return type of apply_filters() based on its optional preceding docblock.
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDoc\PhpDocStringResolver;
use PHPStan\PhpDoc\TypeNodeResolver;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\FileTypeMapper;
use PHPStan\Type\Type;
use PHPStan\Type\MixedType;

class ApplyFiltersDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    protected FileTypeMapper $fileTypeMapper;
    protected PhpDocStringResolver $phpDocStringResolver;
    protected TypeNodeResolver $typeNodeResolver;

    public function __construct(
        FileTypeMapper $fileTypeMapper,
        PhpDocStringResolver $phpDocStringResolver,
        TypeNodeResolver $typeNodeResolver
    ) {
        $this->fileTypeMapper = $fileTypeMapper;
        $this->phpDocStringResolver = $phpDocStringResolver;
        $this->typeNodeResolver = $typeNodeResolver;
    }

    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return in_array(
            $functionReflection->getName(),
            [
                'apply_filters',
                'apply_filters_deprecated',
                'apply_filters_ref_array',
            ],
            true
        );
    }

    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): Type
    {
        $default = new MixedType();
        $comment = self::getNullableNodeComment($functionCall);

        if ($comment === null) {
            return $default;
        }

        // Fetch the docblock contents and resolve it to a PhpDocNode.
        $code = $comment->getText();
        $doc = $this->phpDocStringResolver->resolve($code);

        // Fetch the `@param` values from the docblock.
        $params = $doc->getParamTagValues();

        if (count($params) === 0) {
            return $default;
        }

        $classReflection = $scope->getClassReflection();
        $traitReflection = $scope->getTraitReflection();

        // Need to resolve the docblock in scope in order to get a NameScope object.
        $resolvedPhpDoc = $this->fileTypeMapper->getResolvedPhpDoc(
            $scope->getFile(),
            ($scope->isInTrait() && $traitReflection !== null) ? $traitReflection->getName() : null,
            ($scope->isInClass() && $classReflection !== null) ? $classReflection->getName() : null,
            $scope->getFunctionName(),
            $code
        );

        $nameScope = $resolvedPhpDoc->getNullableNameScope();

        if ($nameScope === null) {
            return $default;
        }

        // Return the Type resolved from the TypeNode.
        return $this->typeNodeResolver->resolve($params[0]->type, $nameScope);
    }

    private static function getNullableNodeComment(FuncCall $node): ?\PhpParser\Comment\Doc
    {
        $startLine = $node->getStartLine();

        while ($node !== null && $node->getStartLine() === $startLine) {
            // Fetch the docblock from the node.
            $comment = $node->getDocComment();

            if ($comment !== null) {
                return $comment;
            }

            /** @var \PhpParser\Node|null */
            $node = $node->getAttribute('parent');
        }

        return null;
    }
}
