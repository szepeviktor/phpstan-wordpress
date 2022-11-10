<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class WP_UnitTestCase_Base extends TestCase
{
    /**
     * Asserts that the given value is an instance of WP_Error.
     *
     * @param mixed  $actual  The value to check.
     * @param string $message Optional. Message to display when the assertion fails.
     * @return void
     */
    public function assertWPError( $actual, $message = '' ) {
    }

    /**
     * Asserts that the given value is not an instance of WP_Error.
     *
     * @param mixed  $actual  The value to check.
     * @param string $message Optional. Message to display when the assertion fails.
     * @return void
     */
    public function assertNotWPError( $actual, $message = '' ) {
    }
}
