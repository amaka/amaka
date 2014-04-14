<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2013-2014 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */

if (! function_exists('includeIfExists')) {
    function includeIfExists($file)
    {
        if (file_exists($file)) {
            return include $file;
        }
        return false;
    }
}

if (! function_exists('tryIncludeAutoloader')) {
    function tryIncludeAutoloader() {
        return includeIfExists(__DIR__ . '/../vendor/autoload.php')
            || includeIfExists(__DIR__ . '/../../../autoload.php');
    }
}

if (! $loader = tryIncludeAutoloader()) {
    file_put_contents(
        sys_get_temp_dir() . '/installer.php',
        file_get_contents('https://getcomposer.org/installer')
        );
    system('php ' . sys_get_temp_dir() . '/installer.php');
    system('php composer.phar install');

    $loader = tryIncludeAutoloader();
}

return $loader;
