<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use PHPStan\Rules\RuleLevelHelper;
use PHPStan\Type\FileTypeMapper;
use SzepeViktor\PHPStan\WordPress\HookDocsRule;

/**
 * @extends \PHPStan\Testing\RuleTestCase<\SzepeViktor\PHPStan\WordPress\HookDocsRule>
 */
class HookDocsRuleTest extends \PHPStan\Testing\RuleTestCase
{
    protected function getRule(): \PHPStan\Rules\Rule
    {
        /** @var \PHPStan\Type\FileTypeMapper */
        $fileTypeMapper = self::getContainer()->getByType(FileTypeMapper::class);

        /** @var \PHPStan\Rules\RuleLevelHelper */
        $ruleLevelHelper = self::getContainer()->getByType(RuleLevelHelper::class);

        // getRule() method needs to return an instance of the tested rule
        return new HookDocsRule($fileTypeMapper, $ruleLevelHelper);
    }

    // phpcs:ignore NeutronStandard.Functions.LongFunction.LongFunction
    public function testRule(): void
    {
        // first argument: path to the example file that contains some errors that should be reported by HookDocsRule
        // second argument: an array of expected errors,
        // each error consists of the asserted error message, and the asserted error file line
        $this->analyse(
            [
                __DIR__ . '/data/hook-docs.php',
            ],
            [
                [
                    'Expected 2 @param tags, found 1.',
                    22,
                ],
                [
                    'Expected 2 @param tags, found 3.',
                    31,
                ],
                [
                    '@param string $one does not accept actual type of parameter: int|string.',
                    43,
                ],
                [
                    '@param string $one does not accept actual type of parameter: int.',
                    53,
                ],
                [
                    '@param tag must not be named $this. Choose a descriptive alias, for example $instance.',
                    82,
                ],
                [
                    'Expected 2 @param tags, found 1.',
                    97,
                ],
                [
                    '@param ChildTestClass $one does not accept actual type of parameter: ParentTestClass.',
                    134,
                ],
                [
                    '@param string $one does not accept actual type of parameter: string|null.',
                    155,
                ],
                [
                    'One or more @param tags has an invalid name or invalid syntax.',
                    170,
                ],
                [
                    'One or more @param tags has an invalid name or invalid syntax.',
                    206,
                ],
            ]
        );
    }
}
