<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2012 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
define('ROOT_DIR', realpath(__DIR__ . '/../'));
define('VENDOR_DIR', ROOT_DIR . '/vendor');


$loader = include VENDOR_DIR . '/autoload.php';
$loader->add('Officine', __DIR__);

return $loader;
