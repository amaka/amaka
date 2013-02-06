<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @Copyright copyright (c) 2012 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
namespace Officine\Amaka\Context;

class CliContext extends \Officine\Amaka\Context
{
    public function __construct()
    {
        parent::__construct();

        $this->setWorkingDirectory(getcwd());
        if (!empty($_SERVER)) {
            $this->setParams($_SERVER);
        }
        if (!empty($_ENV)) {
            $this->setParams($_ENV);
        }
    }
}
