<?php

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function PHPStan\Testing\assertType;

$theme = wp_get_theme();

assertType('string', $theme->get('Name'));
assertType('string', $theme->get('ThemeURI'));
assertType('string', $theme->get('Description'));
assertType('string', $theme->get('Author'));
assertType('string', $theme->get('AuthorURI'));
assertType('string', $theme->get('Version'));
assertType('string', $theme->get('Template'));
assertType('string', $theme->get('Status'));
assertType('array<int, string>', $theme->get('Tags'));
assertType('string', $theme->get('TextDomain'));
assertType('string', $theme->get('DomainPath'));
assertType('string', $theme->get('RequiresWP'));
assertType('false', $theme->get('foo'));
assertType('array<int, string>|string|false', $theme->get($_GET['foo']));
