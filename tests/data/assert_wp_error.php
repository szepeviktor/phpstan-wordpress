<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function PHPStan\Testing\assertType;

class Foo
{

    public function inheritedAssertMethodsNarrowType(): void
    {
        /** @var \WP_Error|int $yes */
        $yes = $yes;

        $customAsserter = new class () extends WP_UnitTestCase_Base {};
        $customAsserter->assertWPError($yes);
        assertType('WP_Error', $yes);
    }

}
