<?php

declare(strict_types=1);

// Locates and loads WordPress core stubs.

foreach ([dirname(__DIR__, 2) . '/php-stubs/wordpress-stubs/wordpress-stubs.php', __DIR__ . '/vendor/php-stubs/wordpress-stubs/wordpress-stubs.php'] as $file) {
    if (file_exists($file)) {
        require_once $file;
        break;
    }
}
