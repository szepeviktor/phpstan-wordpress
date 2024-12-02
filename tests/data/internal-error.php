<?php

/*
 * Intentionally call an undefined function to ensure it does not trigger an internal error.
 * Related issue: https://github.com/szepeviktor/phpstan-wordpress/issues/271
 */
callingUndefinedFunction();
