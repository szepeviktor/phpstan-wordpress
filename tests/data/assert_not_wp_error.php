<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use WP_UnitTestCase_Base;
use function PHPStan\Testing\assertType;

class AssertNotWpError
{

    public function inheritedAssertMethodsNarrowType(): void
    {
        /** @var \WP_Error|int $no */
        $no = $_GET['no'];

        $customAsserter = new class () extends WP_UnitTestCase_Base {};
        $customAsserter->assertNotWPError($no);
        assertType('int', $no);
    }

}
