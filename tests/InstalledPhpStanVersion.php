<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use Composer\InstalledVersions;
use Composer\Semver\Semver;

final class InstalledPhpStanVersion
{
    private string $version;

    public function __construct()
    {
        $version = InstalledVersions::getVersion('phpstan/phpstan-src');

        if ($version === null) {
            throw new \RuntimeException('Cannot determine PHPStan version from Composer.');
        }

        $this->version = $version;
    }

    public function satisfies(string $constraints): bool
    {
        return Semver::satisfies($this->version, $constraints);
    }
}
