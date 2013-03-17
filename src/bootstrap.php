<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2012 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */

if (! function_exists('includeIfExists')) {
    function includeIfExists($file)
    {
        if (file_exists($file)) {
            return include $file;
        }
    }
}

attempt:
if ((!$loader = includeIfExists(__DIR__ . '/../vendor/autoload.php'))
    && (!$loader = includeIfExists(__DIR__ . '/../../../autoload.php'))) {
    file_put_contents(
        sys_get_temp_dir() . '/installer.php',
        file_get_contents('https://getcomposer.org/installer')
        );
    system('php ' . sys_get_temp_dir() . '/installer.php');
    system('php composer.phar install');

    goto attempt;
}


return $loader;
