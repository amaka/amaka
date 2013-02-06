<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2012 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
namespace Officine\Amaka\AmakaScript;

/**
 * Buildfile not found exception
 *
 * @licese    http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2012 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
class AmakaScriptNotFoundException extends \RuntimeException
{
    public function __construct($script, $code = 0, Exception $previous = null)
    {
        $message = sprintf("Amaka script '%s' not found.", $script);
        parent::__construct($message, $code, $previous);
    }
}
