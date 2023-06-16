<?php

/**
 * Set return type of _get_list_table().
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\Type;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\TypeCombinator;

class GetListTableDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    private const CORE_CLASSES = [
        'WP_Posts_List_Table',
        'WP_Media_List_Table',
        'WP_Terms_List_Table',
        'WP_Users_List_Table',
        'WP_Comments_List_Table',
        'WP_Post_Comments_List_Table',
        'WP_Links_List_Table',
        'WP_Plugin_Install_List_Table',
        'WP_Themes_List_Table',
        'WP_Theme_Install_List_Table',
        'WP_Plugins_List_Table',
        'WP_Application_Passwords_List_Table',
        'WP_MS_Sites_List_Table',
        'WP_MS_Users_List_Table',
        'WP_MS_Themes_List_Table',
        'WP_Privacy_Data_Export_Requests_List_Table',
        'WP_Privacy_Data_Removal_Requests_List_Table',
    ];

    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return $functionReflection->getName() === '_get_list_table';
    }

    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): ?Type
    {
        $args = $functionCall->getArgs();

        // Called without $class argument
        if (count($args) < 1) {
            return null;
        }

        $argumentType = $scope->getType($args[0]->value);

        // When called with a $class that isn't a constant string, return default return type
        if (count($argumentType->getConstantStrings()) === 0) {
            return null;
        }

        $types = [];
        foreach ($argumentType->getConstantStrings() as $constantString) {
            $types[] = in_array($constantString->getValue(), self::CORE_CLASSES, true)
                ? new ObjectType($constantString->getValue())
                : new ConstantBooleanType(false);
        }

        return TypeCombinator::union(...$types);
    }
}
