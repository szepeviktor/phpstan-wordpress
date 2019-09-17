<?php declare(strict_types = 1);
/**
 * Fix WordPress stubs.
 */

namespace PHPStan\WordPress\Composer;

use Composer\Script\Event;

class FixWpStubs
{
	const STUBSFILE = '/giacocorsiglia/wordpress-stubs/wordpress-stubs.php';

	public static function php73Polyfill(Event $event): int
	{
		// Bail out if PHP version is lower than 7.3 and Symfony polyfill is not present.
		if (-1 === version_compare(PHP_VERSION, '7.3') && ! class_exists('\Symfony\Polyfill\Php73\Php73')) {
			return 0;
		}

		$io = $event->getIO();
		$io->write('Removing duplicate is_countable() ...');

		$vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
		$stubsFile = $vendorDir . self::STUBSFILE;

		$stubs = file_get_contents($stubsFile);
		if ($stubs === false) {
			$io->writeError('WordPress stubs not found.');
			return 10;
		}
		$fixedStubs = preg_replace('/(\n)(function is_countable)/', '$1// $2', $stubs);

		$numberOfBytes = file_put_contents($stubsFile, $fixedStubs);
		if ($numberOfBytes === false) {
			$io->writeError('FAILED.');
			return 11;
		}

		$io->write('OK.');
		return 0;
	}
}
