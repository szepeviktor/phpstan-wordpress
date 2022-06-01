<?php

/**
 * Exception thrown when validating a hook callback.
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress;

// phpcs:ignore SlevomatCodingStandard.Classes.SuperfluousExceptionNaming.SuperfluousSuffix
class HookCallbackException extends \DomainException
{
}
