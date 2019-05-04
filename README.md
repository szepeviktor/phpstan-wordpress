# WordPress extensions for PHPStan

- [PHPStan](https://github.com/phpstan/phpstan)
- [WordPress](https://wordpress.org/)

### Usage

1. Set up composer, autoload your plugin or theme, see `example/composer.json`
1. Set up PHPStan, see `example/phpstan.neon` - if you don't use composer autoloading add `autoload_files:` and/or `autoload_directories:`
1. Get packages `composer update --classmap-authoritative`
1. Start analysis `vendor/bin/phpstan analyze`
