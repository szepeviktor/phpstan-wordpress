<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function PHPStan\Testing\assertType;

class MySQL2DateAssertions
{

    public function mysql2date(): void
    {

        assertType('int|false', mysql2date('G'));
        assertType('int|false', mysql2date('U'));
        assertType('string|false', mysql2date('Hello'));

        assertType('mixed', $_GET['foo']);
        assertType('int|string|false', mysql2date($_GET['foo']));
    }

}
