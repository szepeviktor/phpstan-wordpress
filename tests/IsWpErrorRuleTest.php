<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use PHPStan\Rules\RuleLevelHelper;
use SzepeViktor\PHPStan\WordPress\IsWpErrorRule;

/**
 * @extends \PHPStan\Testing\RuleTestCase<\SzepeViktor\PHPStan\WordPress\IsWpErrorRule>
 */
class IsWpErrorRuleTest extends \PHPStan\Testing\RuleTestCase
{
    protected function getRule(): \PHPStan\Rules\Rule
    {
        /** @var \PHPStan\Rules\RuleLevelHelper */
        $ruleLevelHelper = self::getContainer()->getByType(RuleLevelHelper::class);

        // getRule() method needs to return an instance of the tested rule
        return new IsWpErrorRule($ruleLevelHelper);
    }

    // phpcs:ignore NeutronStandard.Functions.LongFunction.LongFunction
    public function testRule(): void
    {
        // first argument: path to the example file that contains some errors that should be reported by IsWpErrorRule
        // second argument: an array of expected errors,
        // each error consists of the asserted error message, and the asserted error file line
        $this->analyse(
            [
                __DIR__ . '/data/is_wp_error.php',
            ],
            [
                [
                    'is_wp_error(int) will always evaluate to false.',
                    23,
                ],
                [
                    'is_wp_error(int|false) will always evaluate to false.',
                    24,
                ],
                [
                    'is_wp_error(WP_Error) will always evaluate to true.',
                    25,
                ],
                [
                    'is_wp_error(WP_Post) will always evaluate to false.',
                    26,
                ],
            ]
        );
    }

    public static function getAdditionalConfigFiles(): array
    {
        return [dirname(__DIR__) . '/vendor/szepeviktor/phpstan-wordpress/extension.neon'];
    }
}
