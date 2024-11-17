> [!IMPORTANT]
> Hello everyone! This is Viktor who runs this PHPStan extension. I am planning to stop contributing to the WordPress ecosystem because it is extremely difficult and no one asks me **to join his team** as I am a thinker, a devops person, a tool maker (not a builder).

Please support my work to avoid abandoning this package.

[![Sponsor](https://github.com/szepeviktor/.github/raw/master/.github/assets/github-like-sponsor-button.svg)](https://github.com/sponsors/szepeviktor)

Thank you!

# WordPress Extensions for PHPStan

[![Packagist stats](https://img.shields.io/packagist/dt/szepeviktor/phpstan-wordpress.svg)](https://packagist.org/packages/szepeviktor/phpstan-wordpress/stats)
[![Packagist](https://img.shields.io/packagist/v/szepeviktor/phpstan-wordpress.svg?color=239922&style=popout)](https://packagist.org/packages/szepeviktor/phpstan-wordpress)
[![Tweet](https://img.shields.io/badge/Tweet-share-d5d5d5?style=social&logo=twitter)](https://twitter.com/intent/tweet?text=Static%20analysis%20for%20WordPress&url=https%3A%2F%2Fgithub.com%2Fszepeviktor%2Fphpstan-wordpress)
[![Build Status](https://app.travis-ci.com/szepeviktor/phpstan-wordpress.svg?token=CgYVxsSdNVnRNCDNV3qG&branch=master)](https://app.travis-ci.com/szepeviktor/phpstan-wordpress)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-239922)](https://github.com/phpstan/phpstan)

Static analysis for the WordPress ecosystem.

- [PHPStan](https://phpstan.org/)
- [WordPress](https://wordpress.org/)

## Features

- Enables PHPStan to analyze WordPress plugins and themes.
- Loads the [`php-stubs/wordpress-stubs`](https://github.com/php-stubs/wordpress-stubs) package.
- Provides dynamic return type extensions for functions that are not covered in
    [`php-stubs/wordpress-stubs`](https://github.com/php-stubs/wordpress-stubs/blob/master/functionMap.php)
- Defines some WordPress core constants.
- Validates optional docblocks before `apply_filters()` and `do_action()` calls,
    treating the type of the first `@param` as definitive.

## Requirements

- PHPStan 2.0 or higher
- PHP 7.4 or higher (tested up to PHP 8.3)

## Installation

To use this extension, require it in [Composer](https://getcomposer.org/):

```bash
composer require --dev szepeviktor/phpstan-wordpress
```

If you also install [phpstan/extension-installer](https://github.com/phpstan/extension-installer) then you're all set!

### Manual Installation

If you don't want to use `phpstan/extension-installer`, include `extension.neon` in your project's PHPStan config:

```neon
includes:
    - vendor/szepeviktor/phpstan-wordpress/extension.neon
```

## Configuration

No additional setup is needed. :smiley:
Just configure PHPStan - for example - as shown below:

```neon
parameters:
    level: 5
    paths:
        - plugin.php
        - inc/
```

For more details, visit the [PHPStan Config Reference](https://phpstan.org/config-reference).

:bulb: Use the Composer autoloader or a [custom autoloader](https://github.com/szepeviktor/debian-server-tools/blob/master/webserver/wp-install/wordpress-autoloader.php)!

## Usage

Run the analysis with:

```bash
vendor/bin/phpstan analyze
```

then fix an error and `GOTO 10`!

You find further information in the `examples` directory,
e.g. [`examples/phpstan.neon.dist`](/examples/phpstan.neon.dist)

### Usage in WooCommerce Webshops

Refer to [WooCommerce Stubs](https://github.com/php-stubs/woocommerce-stubs) for specific guidance.

### Usage of an `apply_filters()` Docblock

The WordPress ecosystem often uses PHPDoc docblocks in a non-standard way to
document parameters passed to `apply_filters()`.
Here’s an example:

```php
/**
 * Filters the page title when creating an HTML drop-down list of pages.
 *
 * @param string  $title Page title.
 * @param WP_Post $page  Page data object.
 */
$title = apply_filters( 'list_pages', $title, $page );
```

This extension reads these docblocks and instructs PHPStan to treat the filter’s
return type as certain, based on the first `@param` tag. In this example,
PHPStan interprets `$title` as `string`.

For best results, ensure the first `@param` tag in these docblocks is accurate.

## Make Your Code Testable

- Write clean OOP code: 1 class per file, and no additional code outside `class Name { ... }`.
- Use consistent class naming (WPCS or PSR-4) and store classes in a dedicated `inc/` directory.
- Add precise PHPDoc blocks to classes, properties, methods, functions, and
    `apply_filters()` calls.
- Choose your [main plugin file parts](https://github.com/szepeviktor/starter-plugin).
- Avoid using core constants, use core functions.
- Avoid bad PHP practices, such as:
  - functions: `eval`, `extract`, `compact`, `list`
  - [type juggling](https://www.php.net/manual/en/language.types.type-juggling.php): `$a = '15'; if ($a) ...`
- If you need robust code try avoiding all kinds of type juggling (e.g. `if` needs a boolean),
    see [Variable handling functions](https://www.php.net/manual/en/ref.var.php)
- Avoid enabling `exit_error` in `WP_CLI::launch` or `WP_CLI::runcommand` for improved testability.

## Dirty Corner (FAQ)

WordPress uses conditional function and class definition to allow overrides.
Use `sed` command to exclude function stubs when they are previously defined.

```bash
sed -i -e 's#function is_gd_image#function __is_gd_image#' vendor/php-stubs/wordpress-stubs/wordpress-stubs.php
```
