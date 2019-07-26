# WordPress extensions for PHPStan

[![CircleCI](https://circleci.com/gh/szepeviktor/phpstan-wordpress.svg?style=svg)](https://circleci.com/gh/szepeviktor/phpstan-wordpress) [![Packagist](https://img.shields.io/packagist/v/szepeviktor/phpstan-wordpress.svg?color=239922&style=popout)](https://packagist.org/packages/szepeviktor/phpstan-wordpress)

- [PHPStan](https://github.com/phpstan/phpstan)
- [WordPress](https://wordpress.org/)

### Usage

1. Set up Composer, add `szepeviktor/phpstan-wordpress`, autoload your plugin or theme, see `example/composer.json`
1. Set up PHPStan, see `example/phpstan.neon.dist` - if you don't use Composer autoloading add `autoload_files:` and/or `autoload_directories:`
1. Get packages `composer update --classmap-authoritative`
1. Start analysis `vendor/bin/phpstan analyze`

### What this extension does

- Makes it possible to run PHPStan on WordPress plugins and themes
- Loads giacocorsiglia/wordpress-stubs package
- Defines some core constants
- Handles special functions and classes e.g. `is_wp_error()`

### Make your code testable

- Write clean OOP code: 1 class per file, no other code in class files outside `class Name { ... }`
- Structure your code: uniform class names (WPCS or PSR-4), keep classes in a separate directory `inc/`
- Add proper PHPDoc blocks to classes, properties, methods, functions
- Handle these only in your main plugin file
    - Define constants, e.g. `MYPLUGIN_PATH`
    - Call `register_activation_hook`, `register_deactivation_hook`, `register_uninstall_hook`
    - Class autoloading
    - Load translations
    - Support WP-CLI
    - Decide [what to load](https://github.com/szepeviktor/debian-server-tools/blob/master/webserver/wordpress/_core-is.php#L58-L100)
- Avoid using core constants, use core functions or `MYPLUGIN_PATH`
- Avoid bad parts of PHP
    - functions: eval, extract, compact, list
- If you need robust code try avoiding all kinds of type casting (e.g. `if` needs a boolean),
  see [Variable handling functions](https://www.php.net/manual/en/ref.var.php)
- If you are not bound by PHP 5.x consider following
  [Neutron PHP Standard](https://github.com/Automattic/phpcs-neutron-standard)
