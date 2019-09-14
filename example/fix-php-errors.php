<?php

// Add this to your tests/phpstan/bootstrap.php file.
function phpstan_error_handler( $errno, $errstr, $errfile, $errline ) {
    if ( E_NOTICE !== $errno && strpos($errstr, '/tmp/phpstan/') === false ) {
        throw new \ErrorException( $errstr, 0, $errno, $errfile, $errline );
    }
    return true;
}
set_error_handler( 'phpstan_error_handler' );
