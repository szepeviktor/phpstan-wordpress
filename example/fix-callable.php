<?php
/**
 * Tell PHPStan it is callable type.
 */

/** @var callable $callable */
$callable = [ $instance, $method ];
call_user_func_array( $callable, $args );
add_action( 'hook', $callable );
