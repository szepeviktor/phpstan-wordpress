# WordPress extensions for PHPStan

[![Build Status](https://travis-ci.com/szepeviktor/phpstan-wordpress.svg?branch=master)](https://travis-ci.com/github/szepeviktor/phpstan-wordpress)
[![Packagist](https://img.shields.io/packagist/v/szepeviktor/phpstan-wordpress.svg?color=239922&style=popout)](https://packagist.org/packages/szepeviktor/phpstan-wordpress)
[![Packagist stats](https://img.shields.io/packagist/dt/szepeviktor/phpstan-wordpress.svg)](https://packagist.org/packages/szepeviktor/phpstan-wordpress/stats)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-239922)](https://github.com/phpstan/phpstan)

Static analysis for the WordPress ecosystem.

- [PHPStan](https://phpstan.org/)
- [WordPress](https://wordpress.org/)

### Installation

Add this package to your project.

```bash
composer require --dev szepeviktor/phpstan-wordpress
```

Make PHPStan find it automatically using `phpstan/extension-installer`.

```bash
composer require --dev phpstan/extension-installer
```

Or manually include it in your `phpstan.neon`.

```neon
includes:
    - vendor/szepeviktor/phpstan-wordpress/extension.neon
```

### Configuration

Needs no extra configuration. :smiley: Simply configure PHPStan - for example - this way.

```neon
parameters:
    level: 5
    paths:
        - plugin.php
        - inc/
```

Please read [PHPStan Config Reference](https://phpstan.org/config-reference).

:bulb: Use Composer autoloader or a
[custom autoloader](https://github.com/szepeviktor/debian-server-tools/blob/master/webserver/wp-install/wordpress-autoloader.php)!

### Usage

Just start the analysis: `vendor/bin/phpstan analyze`
then fix an error and `GOTO 10`!

You find futher information in the `example` directory
e.g. [`example/phpstan.neon.dist`](/example/phpstan.neon.dist)

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
- Choose your [main plugin file parts](https://github.com/szepeviktor/small-project/blob/master/MAIN-FILE-PARTS.md).
- Avoid using core constants, use core functions
- Avoid bad parts of PHP
    - functions: `eval`, `extract`, `compact`, `list`
    - [type juggling](https://www.php.net/manual/en/language.types.type-juggling.php): `$a = '15'; if ($a) ...`
- If you need robust code try avoiding all kinds of type juggling (e.g. `if` needs a boolean),
  see [Variable handling functions](https://www.php.net/manual/en/ref.var.php)
- If you are not bound by PHP 5 consider following
  [Neutron Standard](https://github.com/Automattic/phpcs-neutron-standard)
- Do not enable `exit_error` in `WP_CLI::launch` or `WP_CLI::runcommand` to keep your code testable

### Dirty corner (FAQ)

WordPress uses conditional function and class definition for override purposes.
Use `sed` command to exclude function stubs when they are previously defined.

```bash
sed -i -e 's#function is_gd_image#function __is_gd_image#' vendor/php-stubs/wordpress-stubs/wordpress-stubs.php
```
