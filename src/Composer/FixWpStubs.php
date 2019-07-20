<?php declare(strict_types = 1);
/**
 * Fix WordPress stubs.
 */

namespace PHPStan\WordPress\Composer;

use Composer\Script\Event;

class FixWpStubs
{
	const STUBSFILE = '/giacocorsiglia/wordpress-stubs/wordpress-stubs.php';

	public static function php73Polyfill(Event $event)
	{
		if (! class_exists('\Symfony\Polyfill\Php73\Php73')) {
			return;
		}

		echo 'Removing duplicate is_countable() ... ';
		$vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
		$stubsFile = $vendorDir . self::STUBSFILE;

		$stubs = file_get_contents($stubsFile);
		if ($stubs === false) {
			echo 'WordPress stubs not found.';
			return;
		}
		$fixedStubs = preg_replace('/(\n)(function is_countable)/', '$1// $2', $stubs);

		$numberOfBytes = file_put_contents($stubsFile, $fixedStubs);
		$message = 'OK.';
		if ($numberOfBytes === false) {
			$message = 'FAILED.';
		}

		echo $message . "\n";
	}
}
