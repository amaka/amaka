<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2013-2014 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
namespace Officine\Amaka\Contrib;

/**
 * Implement this interface to provide Amaka with a custom name
 * for your invocables.
 */
interface NamedInvocable extends Invocable
{
    /**
     * Returns the name of this invocable.
     *
     * @return string
     */
    public function getName();
}
