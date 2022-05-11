<?php

declare(strict_types=1);

foreach ([__DIR__ . '/../../php-stubs/wordpress-stubs/wordpress-stubs.php', __DIR__ . '/vendor/php-stubs/wordpress-stubs/wordpress-stubs.php'] as $file) {
    if (!file_exists($file)) {
        continue;
    }

    require_once $file;
    break;
}
