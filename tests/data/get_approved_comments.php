<?php

declare( strict_types=1 );

use function PHPStan\Testing\assertType;

assertType( 'array<int, WP_Comment>', get_approved_comments( 1 ) );

assertType( 'int', get_approved_comments( 1, [
    'count' => true,
] ) );
assertType( 'int', get_approved_comments( 1, [
    'count'  => true,
    'fields' => 'ids',
] ) );
assertType( 'array<int, WP_Comment>', get_approved_comments( 1, [
    'count' => false,
] ) );
assertType( 'array<int, int>', get_approved_comments( 1, [
    'fields' => 'ids',
] ) );
assertType( 'array<int, int>', get_approved_comments( 1, [
    'count'  => false,
    'fields' => 'ids',
] ) );
