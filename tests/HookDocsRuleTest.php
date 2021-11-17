<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use PHPStan\Testing\RuleTestCase;
use PHPStan\Type\FileTypeMapper;
use SzepeViktor\PHPStan\WordPress\HookDocsRule;

/**
 * @extends RuleTestCase<HookDocsRule>
 */
class HookDocsRuleTest extends RuleTestCase
{
    protected function getRule(): \PHPStan\Rules\Rule
    {
        /** @var FileTypeMapper */
        $fileTypeMapper = self::getContainer()->getByType(FileTypeMapper::class);

        // getRule() method needs to return an instance of the tested rule
        return new HookDocsRule($fileTypeMapper);
    }

    public function testRule(): void
    {
        // first argument: path to the example file that contains some errors that should be reported by HookDocsRule
        // second argument: an array of expected errors,
        // each error consists of the asserted error message, and the asserted error file line
        $this->analyse(
            [
                __DIR__ . '/data/hook-docs.php'
            ],
            [
                [
                    'Expected 2 @param tags, found 1',
                    14,
                ],
                [
                    'Expected 2 @param tags, found 3',
                    23,
                ],
            ]
        );
    }
}
