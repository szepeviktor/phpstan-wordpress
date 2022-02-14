<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function PHPStan\Testing\assertType;

$term = $_GET['term'] ?? 123;
$taxo = $_GET['taxo'] ?? 'category';

// Empty taxonomy
assertType('string|null', term_exists(123));
assertType('string|null', term_exists(123, ''));
assertType('string|null', term_exists($term));
assertType('string|null', term_exists($term, ''));

// Fixed taxonomy string
assertType('array{term_id: string, term_taxonomy_id: string}|null', term_exists(123, 'category'));
assertType('array{term_id: string, term_taxonomy_id: string}|null', term_exists($term, 'category'));

// Unknown taxonomy type
assertType('array{term_id: string, term_taxonomy_id: string}|string|null', term_exists(123, $taxo));

// Term 0
assertType('0', term_exists(0));
assertType('0', term_exists(0, $taxo));
assertType('0', term_exists(0, 'category'));
assertType('0', term_exists(0, ''));

// Term null
assertType('null', term_exists(null));
assertType('null', term_exists(null, $taxo));
assertType('null', term_exists(null, 'category'));
assertType('null', term_exists(null, ''));

// Oh dear
assertType('mixed', term_exists());
