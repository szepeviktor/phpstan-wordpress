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
                    'Filter callback has no return value.',
                    14,
                ],
                [
                    'Filter callback has no return value.',
                    17,
                ],
                [
                    'Filter callback has no return value.',
                    18,
                ],
                [
                    'Callback expects 1 argument, $accepted_args is set to 0.',
                    21,
                ],
                [
                    'Callback expects 1 argument, $accepted_args is set to 2.',
                    24,
                ],
                [
                    'Callback expects 2 arguments, $accepted_args is set to 1.',
                    24,
                ],
            ]
        );
    }

    public static function getAdditionalConfigFiles(): array
    {
        return [dirname(__DIR__) . '/vendor/szepeviktor/phpstan-wordpress/extension.neon'];
    }
}
