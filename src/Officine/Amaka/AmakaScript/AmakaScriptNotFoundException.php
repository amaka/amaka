<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2013-2014 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
namespace Officine\Amaka\AmakaScript;

use Officine\Amaka\ErrorReporting\Error;

/**
 * Buildfile not found exception
 *
 * @licese    http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2013-2014 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
class AmakaScriptNotFoundException extends Error
{
    public function __construct($script)
    {
        $message = sprintf("Amaka script '%s' not found.", $script);
        parent::__construct($message);
    }
}
