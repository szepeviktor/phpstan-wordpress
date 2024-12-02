<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use PHPStan\Reflection\ReflectionProvider;
use SzepeViktor\PHPStan\WordPress\HookCallbackRule;

use const PHP_VERSION_ID;

/**
 * @extends \PHPStan\Testing\RuleTestCase<\SzepeViktor\PHPStan\WordPress\HookCallbackRule>
 */
class HookCallbackRuleTest extends \PHPStan\Testing\RuleTestCase
{
    private const EXPECTED_ERRORS = [
        ['Filter callback return statement is missing.', 17],
        ['Filter callback return statement is missing.', 18],
        ['Callback expects 1 parameter, $accepted_args is set to 0.', 21],
        ['Callback expects 1 parameter, $accepted_args is set to 2.', 26],
        ['Callback expects 0-1 parameters, $accepted_args is set to 2.', 31],
        ['Callback expects 2 parameters, $accepted_args is set to 1.', 36],
        ['Callback expects 2-4 parameters, $accepted_args is set to 1.', 41],
        ['Callback expects 2-3 parameters, $accepted_args is set to 4.', 46],
        ['Action callback returns true but should not return anything.', 51],
        ['Action callback returns true but should not return anything.', 54],
        ['Filter callback return statement is missing.', 61],
        ['Action callback returns false but should not return anything.', 64],
        ['Action callback returns int but should not return anything.', 67],
        ['Callback expects at least 1 parameter, $accepted_args is set to 0.', 70],
        ['Callback expects at least 1 parameter, $accepted_args is set to 0.', 73],
        ['Callback expects 0-3 parameters, $accepted_args is set to 4.', 78],
        ['Action callback returns int but should not return anything.', 83],
        ['Callback expects 0 parameters, $accepted_args is set to 2.', 86],
        ['Action callback returns false but should not return anything.', 89],
        ['Action callback returns mixed but should not return anything.', 92],
        ['Action callback returns null but should not return anything.', 95],
    ];

    protected function getRule(): \PHPStan\Rules\Rule
    {
        $reflectionProvider = self::getContainer()->getByType(ReflectionProvider::class);

        // getRule() method needs to return an instance of the tested rule
        return new HookCallbackRule($reflectionProvider);
    }

    public function testRule(): void
    {
        // first argument: path to the example file that contains some errors that should be reported by HookCallbackRule
        // second argument: an array of expected errors,
        // each error consists of the asserted error message, and the asserted error file line
        $this->analyse(
            [
                __DIR__ . '/data/hook-callback.php',
                __DIR__ . '/data/internal-error.php',
            ],
            self::EXPECTED_ERRORS
        );
    }

    public function testRuleWithNamedArguments(): void
    {
        if (PHP_VERSION_ID < 80000) {
            $this::markTestSkipped('Named arguments are only supported in PHP 8.0 and later.');
        }

        $this->analyse(
            [
                __DIR__ . '/data/hook-callback-named-args.php',
            ],
            self::EXPECTED_ERRORS
        );
    }

    public static function getAdditionalConfigFiles(): array
    {
        return [dirname(__DIR__) . '/vendor/szepeviktor/phpstan-wordpress/extension.neon'];
    }
}
