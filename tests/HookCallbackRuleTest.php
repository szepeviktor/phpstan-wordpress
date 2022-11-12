<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use PHPStan\Rules\RuleLevelHelper;
use SzepeViktor\PHPStan\WordPress\HookCallbackRule;

/**
 * @extends \PHPStan\Testing\RuleTestCase<\SzepeViktor\PHPStan\WordPress\HookCallbackRule>
 */
class HookCallbackRuleTest extends \PHPStan\Testing\RuleTestCase
{
    protected function getRule(): \PHPStan\Rules\Rule
    {
        /** @var \PHPStan\Rules\RuleLevelHelper */
        $ruleLevelHelper = self::getContainer()->getByType(RuleLevelHelper::class);

        // getRule() method needs to return an instance of the tested rule
        return new HookCallbackRule($ruleLevelHelper);
    }

    // phpcs:ignore NeutronStandard.Functions.LongFunction.LongFunction
    public function testRule(): void
    {
        // first argument: path to the example file that contains some errors that should be reported by HookCallbackRule
        // second argument: an array of expected errors,
        // each error consists of the asserted error message, and the asserted error file line
        $this->analyse(
            [
                __DIR__ . '/data/hook-callback.php',
            ],
            [
                [
                    'Filter callback return statement is missing.',
                    17,
                ],
                [
                    'Filter callback return statement is missing.',
                    20,
                ],
                [
                    'Filter callback return statement is missing.',
                    23,
                ],
                [
                    'Callback expects 1 parameter, $accepted_args is set to 0.',
                    26,
                ],
                [
                    'Callback expects 1 parameter, $accepted_args is set to 2.',
                    31,
                ],
                [
                    'Callback expects 0-1 parameters, $accepted_args is set to 2.',
                    36,
                ],
                [
                    'Callback expects 2 parameters, $accepted_args is set to 1.',
                    41,
                ],
                [
                    'Callback expects 2-4 parameters, $accepted_args is set to 1.',
                    46,
                ],
                [
                    'Callback expects 2-3 parameters, $accepted_args is set to 4.',
                    51,
                ],
                [
                    'Action callback returns true but should not return anything.',
                    56,
                ],
                [
                    'Action callback returns true but should not return anything.',
                    61,
                ],
                [
                    'Filter callback return statement is missing.',
                    68,
                ],
                [
                    'Action callback returns false but should not return anything.',
                    71,
                ],
                [
                    'Action callback returns int but should not return anything.',
                    74,
                ],
                [
                    'Callback expects at least 1 parameter, $accepted_args is set to 0.',
                    77,
                ],
                [
                    'Callback expects at least 1 parameter, $accepted_args is set to 0.',
                    80,
                ],
                [
                    'Callback expects 0-3 parameters, $accepted_args is set to 4.',
                    85,
                ],
                [
                    'Action callback returns int but should not return anything.',
                    90,
                ],
                [
                    'Callback expects 0 parameters, $accepted_args is set to 2.',
                    93,
                ],
                [
                    'Action callback returns false but should not return anything.',
                    96,
                ],
                [
                    'Action callback returns mixed but should not return anything.',
                    99,
                ],
            ]
        );
    }

    public static function getAdditionalConfigFiles(): array
    {
        return [dirname(__DIR__) . '/vendor/szepeviktor/phpstan-wordpress/extension.neon'];
    }
}
