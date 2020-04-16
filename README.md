# WordPress extensions for PHPStan

[![CircleCI](https://circleci.com/gh/szepeviktor/phpstan-wordpress.svg?style=svg)](https://circleci.com/gh/szepeviktor/phpstan-wordpress)
[![Packagist](https://img.shields.io/packagist/v/szepeviktor/phpstan-wordpress.svg?color=239922&style=popout)](https://packagist.org/packages/szepeviktor/phpstan-wordpress)
[![Packagist stats](https://img.shields.io/packagist/dt/szepeviktor/phpstan-wordpress.svg)](https://packagist.org/packages/szepeviktor/phpstan-wordpress/stats)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-239922)](https://github.com/phpstan/phpstan)

Static analysis for the WordPress ecosystem.

- [PHPStan](https://phpstan.org/)
- [WordPress](https://wordpress.org/)

### Usage

1. Set up Composer, add `szepeviktor/phpstan-wordpress`, autoload your plugin or theme, see `example/composer.json`
1. Set up PHPStan, see `example/phpstan.neon.dist` - if you don't use Composer autoloading add `autoload_files:` and/or `autoload_directories:`
1. Get packages `composer update --optimize-autoloader`
1. Start analysis `vendor/bin/phpstan analyze`

### Usage in WooCommerce webshops

Please see [WooCommerce Stubs](https://github.com/php-stubs/woocommerce-stubs)

### What this extension does

- Makes it possible to run PHPStan on WordPress plugins and themes
- Loads [`php-stubs/wordpress-stubs`](https://github.com/php-stubs/wordpress-stubs) package
- Defines some core constants
- Handles special functions and classes e.g. `is_wp_error()`

### Make your code testable

- Write clean OOP code: 1 class per file, no other code in class files outside `class Name { ... }`
- Structure your code: uniform class names (WPCS or PSR-4), keep classes in a separate directory `inc/`
- Add proper PHPDoc blocks to classes, properties, methods, functions
- Handle these only in your [main plugin file](https://github.com/kingkero/wordpress-demoplugin/blob/master/wordpress-demoplugin.php)
    - Define constants, e.g. `MYPLUGIN_PATH`
    - Call `register_activation_hook()`, `register_deactivation_hook()`, `register_uninstall_hook()`
    - Class autoloading
    - Load translations
    - Support WP-CLI
    - Decide [what to load](https://github.com/szepeviktor/Toolkit4WP/blob/master/src/Is.php#L64-L73)
    - Start your plugin in a hook (`plugins_loaded`) - without direct execution
- Avoid using core constants, use core functions or `MYPLUGIN_PATH`
- Avoid bad parts of PHP
    - functions: eval, extract, compact, list
- If you need robust code try avoiding all kinds of type casting (e.g. `if` needs a boolean),
  see [Variable handling functions](https://www.php.net/manual/en/ref.var.php)
- If you are not bound by PHP 5.x consider following
  [Neutron Standard](https://github.com/Automattic/phpcs-neutron-standard)
