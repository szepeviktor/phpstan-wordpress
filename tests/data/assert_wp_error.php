<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use WP_UnitTestCase_Base;
use function PHPStan\Testing\assertType;

class AssertWpError
{

    public function inheritedAssertMethodsNarrowType(): void
    {
        /** @var \WP_Error|int $yes */
        $yes = $_GET['yes'];

        $customAsserter = new class () extends WP_UnitTestCase_Base {};
        $customAsserter->assertWPError($yes);
        assertType('WP_Error', $yes);
    }

}
