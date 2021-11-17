<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

$args = apply_filters( 'filter_with_no_docblock', $one, $two );

/**
 * Too few param tags.
 *
 * @param string $one First parameter.
 */
$args = apply_filters( 'filter', $one, $two );

/**
 * Too many param tags.
 *
 * @param string $one   First parameter.
 * @param string $two   Second parameter.
 * @param string $three Third parameter.
 */
$args = apply_filters( 'filter', $one, $two );
