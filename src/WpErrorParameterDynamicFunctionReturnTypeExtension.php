<?php

/**
 * Set return type of various functions that support a `$wp_error` parameter.
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDoc\TypeStringResolver;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\Type;

class WpErrorParameterDynamicFunctionReturnTypeExtension implements \PHPStan\Type\DynamicFunctionReturnTypeExtension
{
    private const SUPPORTED_FUNCTIONS = [
        'wp_insert_link' => [
            'arg' => 1,
            'false' => '0|positive-int',
            'true' => 'positive-int|WP_Error',
            'maybe' => '0|positive-int|WP_Error',
        ],
        'wp_insert_category' => [
            'arg' => 1,
            'false' => '0|positive-int',
            'true' => 'positive-int|WP_Error',
            'maybe' => '0|positive-int|WP_Error',
        ],
        'wp_set_comment_status' => [
            'arg' => 2,
            'false' => 'bool',
            'true' => 'true|WP_Error',
            'maybe' => 'bool|WP_Error',
        ],
        'wp_update_comment' => [
            'arg' => 1,
            'false' => '0|1|false',
            'true' => '0|1|WP_Error',
            'maybe' => '0|1|false|WP_Error',
        ],
        'wp_schedule_single_event' => [
            'arg' => 3,
            'false' => 'bool',
            'true' => 'true|WP_Error',
            'maybe' => 'bool|WP_Error',
        ],
        'wp_schedule_event' => [
            'arg' => 4,
            'false' => 'bool',
            'true' => 'true|WP_Error',
            'maybe' => 'bool|WP_Error',
        ],
        'wp_reschedule_event' => [
            'arg' => 4,
            'false' => 'bool',
            'true' => 'true|WP_Error',
            'maybe' => 'bool|WP_Error',
        ],
        'wp_unschedule_event' => [
            'arg' => 3,
            'false' => 'bool',
            'true' => 'true|WP_Error',
            'maybe' => 'bool|WP_Error',
        ],
        'wp_clear_scheduled_hook' => [
            'arg' => 2,
            'false' => '0|positive-int|false',
            'true' => '0|positive-int|WP_Error',
            'maybe' => '0|positive-int|false|WP_Error',
        ],
        'wp_unschedule_hook' => [
            'arg' => 1,
            'false' => '0|positive-int|false',
            'true' => '0|positive-int|WP_Error',
            'maybe' => '0|positive-int|false|WP_Error',
        ],
        '_set_cron_array' => [
            'arg' => 1,
            'false' => 'bool',
            'true' => 'true|WP_Error',
            'maybe' => 'bool|WP_Error',
        ],
        'wp_insert_post' => [
            'arg' => 1,
            'false' => '0|positive-int',
            'true' => 'positive-int|WP_Error',
            'maybe' => '0|positive-int|WP_Error',
        ],
        'wp_update_post' => [
            'arg' => 1,
            'false' => '0|positive-int',
            'true' => 'positive-int|WP_Error',
            'maybe' => '0|positive-int|WP_Error',
        ],
        'wp_insert_attachment' => [
            'arg' => 3,
            'false' => '0|positive-int',
            'true' => 'positive-int|WP_Error',
            'maybe' => '0|positive-int|WP_Error',
        ],
    ];

    /** @var \PHPStan\PhpDoc\TypeStringResolver */
    protected $typeStringResolver;

    public function __construct(TypeStringResolver $typeStringResolver)
    {
        $this->typeStringResolver = $typeStringResolver;
    }

    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return array_key_exists($functionReflection->getName(), self::SUPPORTED_FUNCTIONS);
    }

    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): Type
    {
        $name = $functionReflection->getName();
        $functionTypes = self::SUPPORTED_FUNCTIONS[$name] ?? null;
        $args = $functionCall->getArgs();

        if ($functionTypes === null) {
            throw new \PHPStan\ShouldNotHappenException(
                sprintf(
                    'Could not detect return types for function %s()',
                    $name
                )
            );
        }

        $wpErrorArgumentType = new ConstantBooleanType(false);
        $wpErrorArgument = $args[$functionTypes['arg']] ?? null;

        if ($wpErrorArgument !== null) {
            $wpErrorArgumentType = $scope->getType($wpErrorArgument->value);
        }

        // When called with a $wp_error parameter that isn't a constant boolean, return default type
        $type = $functionTypes['maybe'];

        if ($wpErrorArgumentType->isTrue()->yes()) {
            $type = $functionTypes['true'];
        } elseif ($wpErrorArgumentType->isFalse()->yes()) {
            $type = $functionTypes['false'];
        }

        return $this->typeStringResolver->resolve($type);
    }
}
