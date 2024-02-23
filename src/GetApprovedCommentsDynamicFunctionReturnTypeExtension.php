<?php

/**
 * Set return type of get_approved_comments() based on its passed arguments.
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\DynamicFunctionReturnTypeExtension;
use PHPStan\Type\IntegerType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;

class GetApprovedCommentsDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    	/**
	 * @var string[]
	 */
	protected static $supported = [
		'get_approved_comments',
	];


	public function isFunctionSupported( FunctionReflection $functionReflection ): bool {
		return \in_array( $functionReflection->getName(), static::$supported, true );
	}


	/**
	 * - Return 'WP_Comment[]' by default.
	 * - Return `int[]` if `$fields = 'ids'`.
	 * - Return `int` if `$count = true`.
	 *
	 * @link https://developer.wordpress.org/reference/functions/get_approved_comments/#parameters
	 */
	public function getTypeFromFunctionCall( FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope ): ?Type {
		$args = $functionCall->getArgs();

		if ( \count( $args ) < 2 ) {
			return self::defaultType();
		}

		$argumentType = $scope->getType( $args[1]->value );
		if ( $argumentType->isConstantArray()->no() ) {
			return self::getIndeterminedType();
		}

		foreach ( $argumentType->getConstantArrays() as $array ) {
			if ( $array->hasOffsetValueType( new ConstantStringType( 'count' ) )->yes() ) {
				$fieldsValueTypes = $array->getOffsetValueType( new ConstantStringType( 'count' ) );
				if ( $fieldsValueTypes->isTrue()->yes() ) {
					return new IntegerType();
				}
			}
			if ( $array->hasOffsetValueType( new ConstantStringType( 'fields' ) )->yes() ) {
				$fieldsValueTypes = $array->getOffsetValueType( new ConstantStringType( 'fields' ) )->getConstantStrings();
				if ( \count( $fieldsValueTypes ) === 0 ) {
					return self::getIndeterminedType();
				}
				if ( 'ids' === $fieldsValueTypes[0]->getValue() ) {
					return new ArrayType( new IntegerType(), new IntegerType() );
				}
			}
		}

		return self::defaultType();
	}


	protected static function defaultType(): Type {
		return new ArrayType( new IntegerType(), new ObjectType( 'WP_Comment' ) );
	}


	/**
	 * Type defined on the PHPDocs.
	 *
	 * @return Type
	 */
	protected static function getIndeterminedType(): Type {
		return TypeCombinator::union(
			new ArrayType( new IntegerType(), new ObjectType( 'WP_Comment' ) ),
			new ArrayType( new IntegerType(), new IntegerType() ),
			new IntegerType()
		);
	}
}