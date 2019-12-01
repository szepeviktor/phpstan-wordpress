<?php
/**
 * Forward PHP errors to PHPStan.
 */

// Add this to tests/phpstan/bootstrap.php in your project.
function phpstan_error_handler( $errno, $errstr, $errfile, $errline ) {
    if ( E_NOTICE !== $errno && strpos( $errstr, '/tmp/phpstan/' ) === false ) {
        throw new \ErrorException( $errstr, 0, $errno, $errfile, $errline );
    }
    return true;
}
set_error_handler( 'phpstan_error_handler' );
