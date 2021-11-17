<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests\data;

use function PHPStan\Testing\assertType;

class ApplyFiltersTestClass
{
    public function myMethod()
    {
        $foo = 0.0;

        /**
         * Documented filter within a method.
         *
         * @param float $foo Hello, World.
         */
        $value = apply_filters('filter', $foo);
        assertType('float', $value);
    }

    /**
     * This is the method docblock, not the filter docblock. The
     * filter does not have a docblock.
     *
     * @param int $foo Hello, World.
     */
    public function myMethodWithParams(int $foo)
    {
        $value = apply_filters('filter', $foo);
        assertType('mixed', $value);
    }

    /**
     * This is the method docblock, not the filter docblock.
     *
     * @param int $foo Hello, World.
     */
    public function anotherMethodWithParams(int $foo) // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
    {
        $bar = '';

        /**
         * This is the filter docblock.
         *
         * @param string $bar Hello, World.
         */
        $value = apply_filters('filter', $bar);
        assertType('string', $value);
    }
}
