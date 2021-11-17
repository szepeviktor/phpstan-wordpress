<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function PHPStan\Testing\assertType;

$atts = $_GET['atts'] ?? [];

// shortcode_atts filters atts by returning all atts that are occurring in the pairs
// As atts are supposed to be strings the function is expected to return the pair type or a string

assertType('array{}', shortcode_atts([], $atts));
assertType('array{foo: string, bar: string}', shortcode_atts(['foo' => '', 'bar' => ''], $atts));
assertType('array{foo: string, bar: 19|string}', shortcode_atts(['foo' => 'foo-value', 'bar' => 19], $atts));
assertType('array{foo: string|null, bar: 17|string, baz: string}', shortcode_atts(['foo' => null, 'bar' => 17, 'baz' => ''], $atts));
