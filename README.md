# WordPress extensions for PHPStan

[![Packagist stats](https://img.shields.io/packagist/dt/szepeviktor/phpstan-wordpress.svg)](https://packagist.org/packages/szepeviktor/phpstan-wordpress/stats)
[![Packagist](https://img.shields.io/packagist/v/szepeviktor/phpstan-wordpress.svg?color=239922&style=popout)](https://packagist.org/packages/szepeviktor/phpstan-wordpress)
[![Tweet](https://img.shields.io/badge/Tweet-share-d5d5d5?style=social&logo=twitter)](https://twitter.com/intent/tweet?text=Static%20analysis%20for%20WordPress&url=https%3A%2F%2Fgithub.com%2Fszepeviktor%2Fphpstan-wordpress)
[![Build Status](https://travis-ci.com/szepeviktor/phpstan-wordpress.svg?branch=master)](https://travis-ci.com/github/szepeviktor/phpstan-wordpress)
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

### Support my work

Please consider supporting my work.

[![Sponsor](https://github.com/szepeviktor/.github/raw/master/.github/assets/github-like-sponsor-button.svg)](https://github.com/sponsors/szepeviktor)

Thank you!

### Usage

Just start the analysis: `vendor/bin/phpstan analyze`
then fix an error and `GOTO 10`!

You find further information in the `examples` directory
e.g. [`examples/phpstan.neon.dist`](/examples/phpstan.neon.dist)

### Usage in WooCommerce webshops

Please see [WooCommerce Stubs](https://github.com/php-stubs/woocommerce-stubs)

### What this extension does

- Makes it possible to run PHPStan on WordPress plugins and themes
- Loads [`php-stubs/wordpress-stubs`](https://github.com/php-stubs/wordpress-stubs) package
- Provides dynamic return type extensions for many core functions
- Defines some core constants
- Handles special functions and classes e.g. `is_wp_error()`
- Validates the optional docblock that precedes a call to `apply_filters()` and treats the type of its first `@param` as certain

### Usage of an `apply_filters()` docblock

WordPress core - and the wider WordPress ecosystem - uses PHPDoc docblocks
in a non-standard manner to document the parameters passed to `apply_filters()`.
Example:

```php
/**
 * Filters the page title when creating an HTML drop-down list of pages.
 *
 * @param string  $title Page title.
 * @param WP_Post $page  Page data object.
 */
$title = apply_filters( 'list_pages', $title, $page );
```

This extension understands these docblocks when they're present in your code
and uses them to instruct PHPStan to treat the return type of the filter as certain,
according to the first `@param` tag. In the example above this means PHPStan treats the type of `$title` as `string`.

To make the best use of this feature,
ensure that the type of the first `@param` tag in each of these such docblocks is accurate and correct.

### Make your code testable

- Write clean OOP code: 1 class per file, no other code in class files outside `class Name { ... }`
- Structure your code: uniform class names (WPCS or PSR-4), keep classes in a separate directory `inc/`
- Add proper PHPDoc blocks to classes, properties, methods, functions, and calls to `apply_filters()`
- Choose your [main plugin file parts](https://github.com/szepeviktor/starter-plugin).
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
