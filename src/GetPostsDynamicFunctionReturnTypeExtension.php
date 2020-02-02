<?php declare(strict_types = 1);
/**
 * Set return type of get_post().
 */

namespace PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\DynamicFunctionReturnTypeExtension;
use PHPStan\Type\Type;
use PHPStan\Type\ArrayType;
use PHPStan\Type\StringType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\NullType;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\Constant\ConstantStringType;

class GetPostsDynamicFunctionReturnTypeExtension implements DynamicFunctionReturnTypeExtension
{
	public function isFunctionSupported(FunctionReflection $functionReflection): bool
	{
		return in_array($functionReflection->getName(), ['get_posts'], true);
	}

	/**
	 * @see https://developer.wordpress.org/reference/classes/wp_query/#return-fields-parameter
	 */
	public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): Type
	{
		// Called without arguments
		if (count($functionCall->args) === 0) {
			return new ArrayType(new IntegerType(), new ObjectType('WP_Post'));
		}

		$argumentType = $scope->getType($functionCall->args[0]->value);

		// Called with an array argument
		if ($argumentType instanceof ConstantArrayType) {
			$filter = $argumentType['fields'] ?? 'all';
		}
		// Called with a string argument
		if ($argumentType instanceof ConstantStringType) {
			parse_str($argumentType, $variables);
			$filter = $variables['fields'] ?? 'all';
		}

		// Without constant argument return default return type
		if (! isset($filter)) {
			return ParametersAcceptorSelector::selectFromArgs(
				$scope,
				$functionCall->args,
				$functionReflection->getVariants()
			)->getReturnType();
		}

		switch ($filter) {
			case 'ids':
				return new ArrayType(new IntegerType(), new IntegerType());
			case 'id=>parent':
				return new ArrayType(new IntegerType(), new ObjectType('stdClass'));
			case 'all':
			default:
				return new ArrayType(new IntegerType(), new ObjectType('WP_Post'));
		}
	}
}
