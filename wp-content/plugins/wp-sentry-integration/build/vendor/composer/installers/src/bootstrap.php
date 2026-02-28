<?php

namespace WPSentry\ScopedVendor;

use WPSentry\ScopedVendor\Composer\Autoload\ClassLoader;
function includeIfExists(string $file) : ?\WPSentry\ScopedVendor\Composer\Autoload\ClassLoader
{
    if (\file_exists($file)) {
        return include $file;
    }
    return null;
}
if (!($loader = \WPSentry\ScopedVendor\includeIfExists(__DIR__ . '/../vendor/autoload.php')) && !($loader = \WPSentry\ScopedVendor\includeIfExists(__DIR__ . '/../../../autoload.php'))) {
    die('You must set up the project dependencies, run the following commands:' . \PHP_EOL . 'curl -s http://getcomposer.org/installer | php' . \PHP_EOL . 'php composer.phar install' . \PHP_EOL);
}
return $loader;
