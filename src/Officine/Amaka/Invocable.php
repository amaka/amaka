<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2013-2014 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
namespace Officine\Amaka;

interface Invocable extends NamedInvocable
{
    /** NOTE: Please remember to implement the invoke method
     * the prototype has been commented out to allow its signature
     * to be changed.
     *
     * Otherwise we'd force client code to always access the arguments with func_get_args.
     */
    /** public function invoke(..); */
}
